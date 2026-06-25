<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Country;

/**
 * Country normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Country::class)]
final class CountryTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array{0: mixed, 1: mixed}>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[\Override]
    public static function dataProvider(): array
    {
        return [...self::matchingCases(), ...self::fuzzyCases(), ...self::rejectionCases()];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'country';
    }

    /**
     * Country cases that match a code or name.
     *
     * @return array<string, array{0: mixed, 1: mixed}>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function matchingCases(): array
    {
        return [
            'exact iso alpha-2 code'            => ['US', 'US'],
            'exact country name'                => ['United States', 'US'],
            'country name with extra spaces'    => ['  Canada  ', 'CA'],
            'country name with mixed case'      => ['mexico', 'MX'],
            'lowercase country code normalizes' => ['us', 'US'],
            'three character fuzzy boundary'    => ['CUB', 'CU'],
        ];
    }

    /**
     * Country cases resolved by fuzzy matching.
     *
     * @return array<string, array{0: mixed, 1: mixed}>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function fuzzyCases(): array
    {
        return [
            'fuzzy unites states' => ['Unites States', 'US'],
            'fuzzy cnada'         => ['Cnada', 'CA'],
            'fuzzy brasl'         => ['Brasl', 'BR'],
            'fuzzy nw zealand'    => ['Nw Zealand', 'NZ'],
            'fuzzy germny'        => ['Germny', 'DE'],
            'fuzzy frnce'         => ['Frnce', 'FR'],
            'fuzzy itly'          => ['Itly', 'IT'],
            'fuzzy jpan'          => ['Jpan', 'JP'],
            'fuzzy russai'        => ['Russai', 'RU'],
            'fuzzy chnia'         => ['Chnia', 'CN'],
            'fuzzy indai'         => ['Indai', 'IN'],
            'fuzzy austalia'      => ['Austalia', 'AU'],
            'fuzzy swden'         => ['Swden', 'SE'],
            'fuzzy norawy'        => ['Norawy', 'NO'],
            'fuzzy spian'         => ['Spian', 'ES'],
            'fuzzy porutgal'      => ['Porutgal', 'PT'],
            'fuzzy argetina'      => ['Argetina', 'AR'],
            'fuzzy switzrland'    => ['Switzrland', 'CH'],
        ];
    }

    /**
     * Country cases that normalize to null.
     *
     * @return array<string, array{0: mixed, 1: mixed}>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function rejectionCases(): array
    {
        return [
            'underspecified single character' => ['U', null],
            'underspecified two characters'   => ['Un', null],
            'invalid country name'            => ['InvalidCountry', null],
            'empty string'                    => ['', null],
            'only spaces'                     => ['   ', null],
            'null input'                      => [null, null],
        ];
    }
}
