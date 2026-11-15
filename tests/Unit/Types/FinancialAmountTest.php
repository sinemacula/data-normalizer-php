<?php

declare(strict_types = 1);

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
final class FinancialAmountTest extends TypeTestCase
{
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
            'integer converts to minor units'          => [12, 1200],
            'float converts to minor units'            => [12.34, 1234],
            'negative float converts'                  => [-12.34, -1234],
            'zero converts to zero'                    => [0, 0],
            'numeric string converts'                  => ['12.34', 1234],
            'padded numeric string converts'           => [' 12.34 ', 1234],
            'exponent string converts'                 => ['1e2', 10000],
            'half rounds away from zero'               => [0.125, 13],
            'negative half rounds away from zero'      => [-0.125, -13],
            'float precision is rounded'               => [19.99, 1999],
            'non numeric string returns null'          => ['12.34 USD', null],
            'empty string returns null'                => ['', null],
            'null input returns null'                  => [null, null],
            'boolean input returns null'               => [true, null],
            'array input returns null'                 => [[], null],
            'object input returns null'                => [(object) ['x' => 'y'], null],
            'infinite input returns null'              => [INF, null],
            'not a number input returns null'          => [NAN, null],
            'amount beyond integer range returns null' => [1e20, null],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'financialAmount';
    }
}
