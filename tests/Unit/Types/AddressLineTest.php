<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\AddressLine;

/**
 * Address line normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(AddressLine::class)]
class AddressLineTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'title case normalization'     => ['123 main st.', '123 Main St.'],
            'trim and normalize spacing'   => ['  456  Elm Street  ', '456 Elm Street'],
            'uppercase words normalized'   => ['APARTMENT 5A', 'Apartment 5a'],
            'only spaces returns null'     => ['   ', null],
            'null input returns null'      => [null, null],
            'po box casing is normalized'  => ['PO Box 123', 'Po Box 123'],
            'hyphenated street normalized' => [
                '123-martin luther king jr. blvd',
                '123-Martin Luther King Jr. Blvd',
            ],
            'apostrophes are preserved'             => ['john\'s street', 'John\'s Street'],
            'mixed case with apostrophe normalized' => ['St. JOHN\'S AVENUE', 'St. John\'s Avenue'],
            'trailing comma removed'                => ['123 main st,', '123 Main St'],
            'trailing comma with space removed'     => ['123 main st ,', '123 Main St'],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'addressLine';
    }
}
