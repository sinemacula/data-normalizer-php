<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\PostalCode;

/**
 * Postal code normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(PostalCode::class)]
class PostalCodeTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            // No country — cleanup only (uppercase, trim, collapse), no validation.
            'no country uppercases value'         => ['sw1a 1aa', 'SW1A 1AA'],
            'no country trims surrounding spaces' => ['  12345  ', '12345'],
            'empty string returns null'           => ['', null],
            'spaces only returns null'            => ['   ', null],
            'null input returns null'             => [null, null],
            'non-string returns null'             => [12345, null],
            // Country supplied — validated and formatted via brick/postcode.
            'us five digit zip is valid'                => ['33602', '33602', 'US'],
            'us zip plus four is hyphenated'            => ['337014313', '33701-4313', 'US'],
            'lowercase country code is resolved'        => ['33602', '33602', 'us'],
            'gb postcode gains canonical space'         => ['sw1a1aa', 'SW1A 1AA', 'GB'],
            'ca postcode gains canonical space'         => ['k1a0b1', 'K1A 0B1', 'CA'],
            'nl postcode gains canonical space'         => ['1234ab', '1234 AB', 'NL'],
            'invalid postcode for country returns null' => ['3360', null, 'US'],
            'country without postcode system cleans up' => ['abc 123', 'ABC 123', 'HK'],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'postalCode';
    }
}
