<?php

declare(strict_types = 1);

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
final class DateTest extends TypeTestCase
{
    /** @var string The canonical normalized date value. */
    private const string NORMALIZED_DATE = '2024-01-01';

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
            'iso date remains unchanged'                           => [self::NORMALIZED_DATE, self::NORMALIZED_DATE],
            'us date format is normalized'                         => ['01/01/2024', self::NORMALIZED_DATE],
            'dotted date format is normalized'                     => ['2024.01.01', self::NORMALIZED_DATE],
            'long textual date is normalized'                      => ['January 1, 2024', self::NORMALIZED_DATE],
            'ordinal textual date is normalized'                   => ['1st January 2024', self::NORMALIZED_DATE],
            'leading and trailing spaces are trimmed'              => [' 2024-01-01 ', self::NORMALIZED_DATE],
            'datetime expression normalizes to calendar date'      => ['2024-01-01T18:30:00+00:00', self::NORMALIZED_DATE],
            'non date text returns null'                           => ['not a date', null],
            'invalid iso calendar date returns null'               => ['2024-02-30', null],
            'rollover iso calendar date returns null'              => ['2024-02-31', null],
            'invalid dotted calendar date returns null'            => ['2024.02.30', null],
            'invalid us calendar date returns null'                => ['02/30/2024', null],
            'invalid long calendar date returns null'              => ['February 30, 2024', null],
            'invalid ordinal calendar date returns null'           => ['30th February 2024', null],
            'null input returns null'                              => [null, null],
            'empty string returns null'                            => ['', null],
            'spaces only returns null'                             => ['   ', null],
            'relative day is rejected when absolute only'          => ['tomorrow', null, ['relative' => false]],
            'relative weekday is rejected when absolute only'      => ['next monday', null, ['relative' => false]],
            'timestamp is rejected when absolute only'             => ['@1790000000', null, ['relative' => false]],
            'zero timestamp is rejected when absolute only'        => ['@0', null, ['relative' => false]],
            'ordinal date is rejected when absolute only'          => ['2026-271', null, ['relative' => false]],
            'timestamp keeps its date by default'                  => ['@1790000000', '2026-09-21', null],
            'offset expression is rejected when absolute only'     => ['2026-09-28 +1 week', null, ['relative' => false]],
            'offset expression keeps rolling by default'           => ['2026-09-28 +1 week', '2026-10-05', null],
            'month edge expression is rejected when absolute'      => ['last day of february 2026', null, ['relative' => false]],
            'iso week is rejected when absolute only'              => ['2026W40', null, ['relative' => false]],
            'time only is rejected when absolute only'             => ['10:00', null, ['relative' => false]],
            'year only is rejected when absolute only'             => ['2026', null, ['relative' => false]],
            'us date is accepted when absolute only'               => ['09/28/2026', '2026-09-28', ['relative' => false]],
            'day first date is rejected when absolute only'        => ['28/09/2026', null, ['relative' => false]],
            'invalid date is rejected when absolute only'          => ['2026-02-30', null, ['relative' => false]],
            'offset datetime keeps its local date'                 => ['2026-09-28T23:30:00-05:00', '2026-09-28', null],
            'offset datetime keeps its local date when absolute'   => ['2026-09-28T23:30:00-05:00', '2026-09-28', ['relative' => false]],
            'date with time is accepted when absolute only'        => ['2026-09-28 10:00', '2026-09-28', ['relative' => false]],
            'matching weekday is accepted when absolute only'      => ['Mon, 28 Sep 2026 10:00:00 +0000', '2026-09-28', ['relative' => false]],
            'mismatched weekday is rejected when absolute only'    => ['Tue, 28 Sep 2026', null, ['relative' => false]],
            'mismatched weekday keeps rolling by default'          => ['Tue, 28 Sep 2026', '2026-09-29', null],
            'rolled over datetime is rejected when absolute'       => ['2026-06-31T10:00:00Z', null, ['relative' => false]],
            'month and year resolve to the first when absolute'    => ['September 2026', '2026-09-01', ['relative' => false]],
            'relative true keeps the default behaviour'            => ['2026-09-28 +1 week', '2026-10-05', ['relative' => true]],
            'falsy relative keeps the default behaviour'           => ['2026-09-28 +1 week', '2026-10-05', ['relative' => 0]],
            'other options keep the default behaviour'             => ['2026-09-28 +1 week', '2026-10-05', ['format' => 'Y-m-d']],
            'month edge on its own date is rejected when absolute' => ['first day of september 2026', null, ['relative' => false]],
            'month edge on its own date resolves by default'       => ['first day of september 2026', '2026-09-01', null],
            'non date text is rejected when absolute only'         => ['not a date', null, ['relative' => false]],
            'string context keeps the default behaviour'           => ['2026-09-28 +1 week', '2026-10-05', 'GB'],
        ];
    }

    /**
     * Test that relative dates still resolve from the clock by default.
     *
     * @return void
     */
    public function testRelativeDatesResolveFromTheClockByDefault(): void
    {
        $before = (new \DateTimeImmutable('tomorrow'))->format('Y-m-d');
        $date   = Date::normalize('tomorrow');
        $after  = (new \DateTimeImmutable('tomorrow'))->format('Y-m-d');

        self::assertContains($date, [$before, $after]);
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'date';
    }
}
