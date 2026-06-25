<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use SineMacula\Foundation\Normalizers\Normalizer;
use Tests\Unit\UnitTestCase;

/**
 * Type test case.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
abstract class TypeTestCase extends UnitTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<array-key, array<int, mixed>>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    abstract public static function dataProvider(): iterable;

    /**
     * Test that the normalizer produces the expected result.
     *
     * @param  mixed  $input
     * @param  mixed  $expected
     * @param  mixed|null  $context
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[DataProvider('dataProvider')]
    public function testNormalizesValueToExpectedResult(mixed $input, mixed $expected, mixed $context = null): void
    {
        static::assertSame($expected, Normalizer::__callStatic($this->getNormalizerName(), [$input, $context]));
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    abstract protected function getNormalizerName(): string;
}
