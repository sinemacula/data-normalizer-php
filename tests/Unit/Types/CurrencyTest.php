<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Currency;

/**
 * Currency normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Currency::class)]
class CurrencyTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'uppercase code remains unchanged'       => ['USD', 'USD'],
            'lowercase code is uppercased'           => ['usd', 'USD'],
            'mixed case code with spaces normalizes' => [' UsD ', 'USD'],
            'eur code remains unchanged'             => ['EUR', 'EUR'],
            'gbp code is uppercased'                 => ['gbp', 'GBP'],
            'jpy mixed case is uppercased'           => ['jPy', 'JPY'],
            'short code returns null'                => ['US', null],
            'word currency returns null'             => ['DOLLAR', null],
            'euro word returns null'                 => ['EURO', null],
            'unknown code returns null'              => ['ZZZ', null],
            'spaces only returns null'               => ['   ', null],
            'empty string returns null'              => ['', null],
            'null input returns null'                => [null, null],
            'integer input returns null'             => [123, null],
            'boolean input returns null'             => [true, null],
            'array input returns null'               => [[], null],
            'object input returns null'              => [(object) ['x' => 'y'], null],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'currency';
    }
}
