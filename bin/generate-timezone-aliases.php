<?php

declare(strict_types = 1);

/*
 * Generate resources/timezone-aliases.json from an IANA tzdata release.
 *
 * Usage: php bin/generate-timezone-aliases.php <tzdata-directory>
 *
 * The directory is an extracted tzdata release (for example
 * https://data.iana.org/time-zones/releases/tzdata2026a.tar.gz) whose version
 * matches the PHP timezone database (timezone_version_get()).
 *
 * Every identifier PHP lists only under DateTimeZone::ALL_WITH_BC gets an
 * entry. A 'backzone' link to a listed identifier wins, since it keeps the
 * country the alias names. Otherwise links from the 'backward' and 'etcetera'
 * files resolve to the first identifier in DateTimeZone::listIdentifiers(),
 * following each '#=' target first. Abbreviations and identifiers with no
 * listed equivalent map to null so the normalizer keeps rejecting them.
 */

// Zone abbreviations stay rejected even though tzdata links them to a zone.
$abbreviations = ['CET', 'EET', 'EST', 'HST', 'MET', 'MST', 'WET'];

$directory = $argv[1] ?? null;

if (!is_string($directory) || !is_file($directory . '/backward') || !is_file($directory . '/backzone') || !is_file($directory . '/version')) {
    fwrite(STDERR, "Usage: php bin/generate-timezone-aliases.php <tzdata-directory>\n");

    exit(1);
}

$release = trim((string) file_get_contents($directory . '/version'));

if (str_replace('.', '', timezone_version_get()) !== preg_replace_callback('/[a-z]$/', static fn (array $letter): string => (string) (ord($letter[0]) - 96), $release)) {
    fwrite(STDERR, 'tzdata ' . $release . ' does not match the PHP timezone database ' . timezone_version_get() . ".\n");

    exit(1);
}

$links = [];

foreach (['etcetera', 'backward'] as $file) {
    foreach ((array) file($directory . '/' . $file) as $line) {

        if (preg_match('/^Link\s+(\S+)\s+(\S+)(?:\s+#=\s+(\S+))?/', (string) $line, $matches) !== 1) {
            continue;
        }

        $links[$matches[2]] = $matches[3] ?? $matches[1];
    }
}

$preferred = [];

foreach ((array) file($directory . '/backzone') as $line) {

    if (preg_match('/^(?:#PACKRATLIST zone\.tab )?Link\s+(\S+)\s+(\S+)/', (string) $line, $matches) !== 1) {
        continue;
    }

    $preferred[$matches[2]] = $matches[1];
}

$listed     = DateTimeZone::listIdentifiers();
$linkedFrom = [];

foreach ($links as $alias => $target) {

    if (!in_array($alias, $listed, true)) {
        continue;
    }

    $linkedFrom[$target][] = $alias;
}

$aliases = [];

foreach (array_diff(DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), $listed) as $identifier) {

    $canonical = in_array($preferred[$identifier] ?? null, $listed, true) ? $preferred[$identifier] : $identifier;

    while (!in_array($canonical, $listed, true) && isset($links[$canonical])) {
        $canonical = $links[$canonical];
    }

    if (!in_array($canonical, $listed, true)) {
        $canonical = count($linkedFrom[$canonical] ?? []) === 1 ? $linkedFrom[$canonical][0] : null;
    }

    $aliases[$identifier] = in_array($identifier, $abbreviations, true) ? null : $canonical;
}

ksort($aliases);

file_put_contents(dirname(__DIR__) . '/resources/timezone-aliases.json', json_encode($aliases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

fwrite(STDOUT, count($aliases) . ' identifiers written from tzdata ' . $release . ".\n");
