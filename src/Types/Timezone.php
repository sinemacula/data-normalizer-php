<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Concerns\LoadsJsonResources;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The timezone normalizer.
 *
 * Canonical identifiers from DateTimeZone::listIdentifiers() are returned as
 * they are. Backward-compatible links such as US/Eastern resolve to their
 * listed equivalent through resources/timezone-aliases.json, which is generated
 * from the IANA tzdata 'backward', 'backzone' and 'etcetera' files (currently
 * 2026a) by running 'php bin/generate-timezone-aliases.php <tzdata-directory>'.
 * Abbreviations such as EST, and identifiers with no listed equivalent such as
 * Etc/GMT+5, map to null there and stay rejected.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @managed-static
 *
 * @inheritable
 */
class Timezone implements NormalizerInterface
{
    use LoadsJsonResources;

    /** @var array<array-key, string|null>|null Canonical identifiers keyed by lowercase alias. */
    private static ?array $aliases = null;

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        if (!$value) {
            return null;
        }

        $timezones = \DateTimeZone::listIdentifiers();

        foreach ($timezones as $timezone) {
            if (strcasecmp($value, $timezone) === 0) {
                return $timezone;
            }
        }

        return self::resolveAlias($value, $timezones);
    }

    /**
     * Resolve a backward-compatible alias to its listed identifier.
     *
     * The target must also be listed by the running PHP, whose timezone
     * database can be older than the one the aliases were generated from.
     *
     * @param  string  $value
     * @param  array<int, string>  $timezones
     * @return string|null
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    private static function resolveAlias(string $value, array $timezones): ?string
    {
        $canonical = self::getAliases()[strtolower($value)] ?? null;

        return in_array($canonical, $timezones, true) ? $canonical : null;
    }

    /**
     * Return the canonical identifiers keyed by lowercase alias.
     *
     * @return array<array-key, string|null>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    private static function getAliases(): array
    {
        if (self::$aliases === null) {

            self::$aliases = [];

            foreach (self::loadJson('timezone-aliases') as $alias => $canonical) {
                self::$aliases[strtolower((string) $alias)] = $canonical;
            }
        }

        return self::$aliases;
    }
}
