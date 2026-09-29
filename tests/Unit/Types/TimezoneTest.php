<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Timezone;

/**
 * Timezone normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Timezone::class)]
final class TimezoneTest extends TypeTestCase
{
    /**
     * Set up the test case.
     *
     * Clears the memoised aliases so every test loads them itself.
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        (new \ReflectionProperty(Timezone::class, 'aliases'))->setValue(null, null);
    }

    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[\Override]
    public static function dataProvider(): array
    {
        return [
            'canonical timezone remains unchanged' => ['America/New_York', 'America/New_York'],
            'timezone is normalized by case'       => ['europe/london', 'Europe/London'],
            'invalid timezone returns null'        => ['Invalid/Timezone', null],
            'empty string returns null'            => ['', null],
            'spaces only returns null'             => ['   ', null],
            'null input returns null'              => [null, null],
            'timezone with spaces is trimmed'      => ['  Asia/Tokyo  ', 'Asia/Tokyo'],
            'legacy us alias resolves'             => ['US/Eastern', 'America/New_York'],
            'legacy alias resolves by case'        => ['asia/calcutta', 'Asia/Kolkata'],
            'renamed zone resolves'                => ['Europe/Kiev', 'Europe/Kyiv'],
            'country alias keeps its own zone'     => ['Iceland', 'Atlantic/Reykjavik'],
            'merged alias keeps its country'       => ['Pacific/Yap', 'Pacific/Chuuk'],
            'merged canadian alias stays canadian' => ['America/Coral_Harbour', 'America/Atikokan'],
            'posix rule name resolves'             => ['EST5EDT', 'America/New_York'],
            'utc alias resolves to utc'            => ['Etc/UTC', 'UTC'],
            'zulu resolves to utc'                 => ['Zulu', 'UTC'],
            'zone abbreviation returns null'       => ['EST', null],
            'unknown abbreviation returns null'    => ['PST', null],
            'gmt abbreviation returns null'        => ['GMT', null],
            'unlisted fixed offset returns null'   => ['Etc/GMT+5', null],
            'placeholder zone returns null'        => ['Factory', null],
            'windows name returns null'            => ['Eastern Standard Time', null],
        ];
    }

    /**
     * Test that every backward-compatible identifier is listed or mapped.
     *
     * Fails when a timezone database update adds a link, so the alias map is
     * regenerated rather than the new link silently returning null. PHP builds
     * that read the system timezone database also list its support files (such
     * as localtime and tzdata.zi); every IANA identifier starts with an
     * uppercase letter, so those are skipped.
     *
     * @return void
     */
    public function testEveryBackwardCompatibleIdentifierIsListedOrMapped(): void
    {
        $aliases     = self::loadAliases();
        $identifiers = preg_grep('/^[A-Z]/', \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC));
        $unmapped    = array_filter(
            (array) $identifiers,
            static fn (string $identifier): bool => !in_array($identifier, \DateTimeZone::listIdentifiers(), true) && !array_key_exists($identifier, $aliases),
        );

        self::assertSame([], array_values($unmapped));
    }

    /**
     * Test that every resolved alias is a listed identifier.
     *
     * @return void
     */
    public function testResolvedAliasesAreListedIdentifiers(): void
    {
        foreach (array_keys(self::loadAliases()) as $alias) {

            $resolved = Timezone::normalize($alias);

            self::assertTrue($resolved === null || in_array($resolved, \DateTimeZone::listIdentifiers(), true), $alias);
        }
    }

    /**
     * Test that every resolved alias keeps the offsets of the zone it names.
     *
     * @return void
     *
     * @throws \DateInvalidTimeZoneException
     */
    public function testResolvedAliasesKeepTheirOffsets(): void
    {
        $instants = [new \DateTimeImmutable('2026-01-15 12:00:00 UTC'), new \DateTimeImmutable('2026-07-15 12:00:00 UTC')];

        foreach (array_keys(self::loadAliases()) as $alias) {

            $resolved = Timezone::normalize($alias);

            if ($resolved === null || !in_array($alias, \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC), true)) {
                continue;
            }

            foreach ($instants as $instant) {
                self::assertSame((new \DateTimeZone($alias))->getOffset($instant), (new \DateTimeZone($resolved))->getOffset($instant), $alias);
            }
        }
    }

    /**
     * Test that an alias whose target is not listed returns null.
     *
     * The reflection coupling is deliberate: an older timezone database than
     * the aliases were generated from cannot be installed for the test.
     *
     * @return void
     */
    public function testAliasWithUnlistedTargetReturnsNull(): void
    {
        (new \ReflectionProperty(Timezone::class, 'aliases'))->setValue(null, ['legacy/zone' => 'Unlisted/Zone']);

        self::assertNull(Timezone::normalize('Legacy/Zone'));
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'timezone';
    }

    /**
     * Load the committed alias map.
     *
     * @return array<string, string|null>
     */
    private static function loadAliases(): array
    {
        /** @var array<string, string|null> */
        return json_decode((string) file_get_contents(__DIR__ . '/../../../resources/timezone-aliases.json'), true, 512, JSON_THROW_ON_ERROR);
    }
}
