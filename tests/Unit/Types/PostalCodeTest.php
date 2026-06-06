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
    /** @var string The canonical ZIP value used in baseline examples. */
    private const string STANDARD_ZIP = '12345';

    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'standard zip remains unchanged'             => [self::STANDARD_ZIP, self::STANDARD_ZIP],
            'zip plus four remains unchanged'            => ['12345-6789', '12345-6789'],
            'lowercase alphanumeric code is uppercased'  => ['sw1a 1aa', 'SW1A 1AA'],
            'mixed case alphanumeric code is uppercased' => ['Sw1A 1aA', 'SW1A 1AA'],
            'postal code with spaces is trimmed'         => ['  12345  ', self::STANDARD_ZIP],
            'alphanumeric zip remains uppercased'        => ['12ab5', '12AB5'],
            'empty string returns null'                  => ['', null],
            'spaces only returns null'                   => ['   ', null],
            'null input returns null'                    => [null, null],
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
