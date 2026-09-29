<?php

declare(strict_types = 1);

namespace Tests\Unit\Concerns;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use SineMacula\Foundation\Normalizers\Concerns\ConvertsCase;
use Tests\Fixtures\CaseConverter;
use Tests\Unit\UnitTestCase;

/**
 * Converts case trait test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversTrait(ConvertsCase::class)]
final class ConvertsCaseTest extends UnitTestCase
{
    /**
     * Test that valid UTF-8 is converted with mbstring.
     *
     * @return void
     */
    public function testValidUtf8IsMultibyte(): void
    {
        self::assertTrue(CaseConverter::canConvertMultibyte('José'));
    }

    /**
     * Test that invalid UTF-8 is not converted with mbstring.
     *
     * @return void
     */
    public function testInvalidUtf8IsNotMultibyte(): void
    {
        self::assertFalse(CaseConverter::canConvertMultibyte("J\xC9SUS"));
    }

    /**
     * Lowercase data provider.
     *
     * @return iterable<string, array{string, string}>
     */
    public static function lowerProvider(): iterable
    {
        yield from [
            'ascii letters'           => ['JOHN', 'john'],
            'accented letters'        => ['JOSÉ ÑUÑEZ', 'josé ñuñez'],
            'invalid utf-8 byte-wise' => ["J\xC9SUS", "j\xC9sus"],
        ];
    }

    /**
     * Test that the value is converted to lowercase.
     *
     * @param  string  $value
     * @param  string  $expected
     * @return void
     */
    #[DataProvider('lowerProvider')]
    public function testConvertsToLowercase(string $value, string $expected): void
    {
        self::assertSame($expected, CaseConverter::lower($value));
    }

    /**
     * Upper first data provider.
     *
     * @return iterable<string, array{string, string}>
     */
    public static function upperFirstProvider(): iterable
    {
        yield from [
            'ascii letter'                 => ['john', 'John'],
            'accented letter'              => ['émile', 'Émile'],
            'eñe'                          => ['ñuñez', 'Ñuñez'],
            'leading dotted capital i'     => ["i\u{0307}smai\u{0307}l", 'İsmail'],
            'inner dotted capital i'       => ["ibrahi\u{0307}m", 'Ibrahim'],
            'multi-letter title case kept' => ['ßolvig', 'ßolvig'],
            'invalid utf-8 byte-wise'      => ["j\xC9sus", "J\xC9sus"],
            'empty string'                 => ['', ''],
        ];
    }

    /**
     * Test that the first letter is uppercased.
     *
     * @param  string  $value
     * @param  string  $expected
     * @return void
     */
    #[DataProvider('upperFirstProvider')]
    public function testUppercasesFirstLetter(string $value, string $expected): void
    {
        self::assertSame($expected, CaseConverter::first($value));
    }
}
