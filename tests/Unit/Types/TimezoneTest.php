<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Timezone;

/**
 * Timezone normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Timezone::class)]
final class TimezoneTest extends TypeTestCase
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
            'canonical timezone remains unchanged' => ['America/New_York', 'America/New_York'],
            'timezone is normalized by case'       => ['europe/london', 'Europe/London'],
            'invalid timezone returns null'        => ['Invalid/Timezone', null],
            'empty string returns null'            => ['', null],
            'spaces only returns null'             => ['   ', null],
            'null input returns null'              => [null, null],
            'timezone with spaces is trimmed'      => ['  Asia/Tokyo  ', 'Asia/Tokyo'],
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
        return 'timezone';
    }
}
