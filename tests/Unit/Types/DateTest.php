<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Date;

/**
 * Date normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Date::class)]
class DateTest extends TypeTestCase
{
    /** @var string The canonical normalized date value. */
    private const string NORMALIZED_DATE = '2024-01-01';

    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'iso date remains unchanged'                      => [self::NORMALIZED_DATE, self::NORMALIZED_DATE],
            'us date format is normalized'                    => ['01/01/2024', self::NORMALIZED_DATE],
            'dotted date format is normalized'                => ['2024.01.01', self::NORMALIZED_DATE],
            'long textual date is normalized'                 => ['January 1, 2024', self::NORMALIZED_DATE],
            'ordinal textual date is normalized'              => ['1st January 2024', self::NORMALIZED_DATE],
            'leading and trailing spaces are trimmed'         => [' 2024-01-01 ', self::NORMALIZED_DATE],
            'datetime expression normalizes to calendar date' => ['2024-01-01T18:30:00+00:00', self::NORMALIZED_DATE],
            'non date text returns null'                      => ['not a date', null],
            'invalid iso calendar date returns null'          => ['2024-02-30', null],
            'rollover iso calendar date returns null'         => ['2024-02-31', null],
            'invalid dotted calendar date returns null'       => ['2024.02.30', null],
            'invalid us calendar date returns null'           => ['02/30/2024', null],
            'invalid long calendar date returns null'         => ['February 30, 2024', null],
            'invalid ordinal calendar date returns null'      => ['30th February 2024', null],
            'null input returns null'                         => [null, null],
            'empty string returns null'                       => ['', null],
            'spaces only returns null'                        => ['   ', null],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'date';
    }
}
