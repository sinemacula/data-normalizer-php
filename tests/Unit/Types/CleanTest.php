<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Clean;

/**
 * Clean normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Clean::class)]
class CleanTest extends TypeTestCase
{
    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'trim leading and trailing spaces' => [
                '   leading and trailing spaces   ',
                'leading and trailing spaces',
            ],
            'collapse multiple spaces'             => ['multiple    spaces', 'multiple spaces'],
            'single spaces stay unchanged'         => ['single space', 'single space'],
            'tabs and newlines collapse to spaces' => [" tabs\tand\nnewlines ", 'tabs and newlines'],
            'mixed whitespace is normalized'       => ["  mixed \t whitespace  ", 'mixed whitespace'],
            'only spaces returns null'             => ['    ', null],
            'null input returns null'              => [null, null],
            'integer input returns null'           => [123, null],
            'simple text unchanged'                => ['simple text', 'simple text'],
            'string zero is preserved'             => ['0', '0'],
            'empty string returns null'            => ['', null],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'clean';
    }
}
