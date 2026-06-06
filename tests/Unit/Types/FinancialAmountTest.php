<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\FinancialAmount;

/**
 * Financial amount normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(FinancialAmount::class)]
class FinancialAmountTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'integer amount converts to minor units'                           => [100, 10000],
            'float amount converts to minor units'                             => [123.45, 12345],
            'numeric string float converts to minor units'                     => ['678.90', 67890],
            'numeric string integer converts to minor units'                   => ['1000', 100000],
            'negative integer amount converts to minor units'                  => [-50, -5000],
            'negative float amount converts to minor units'                    => [-123.45, -12345],
            'negative numeric string float converts to minor units'            => ['-678.90', -67890],
            'float with extra precision rounds to nearest minor unit'          => [123.456, 12346],
            'numeric string with extra precision rounds to nearest minor unit' => ['123.456', 12346],
            'half-down value rounds to nearest minor unit'                     => ['10.554', 1055],
            'non numeric string returns null'                                  => ['not a number', null],
            'null input returns null'                                          => [null, null],
            'empty string returns null'                                        => ['', null],
            'spaces only returns null'                                         => ['   ', null],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'financialAmount';
    }
}
