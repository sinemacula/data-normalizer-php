<?php

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Phone;

/**
 * Phone normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Phone::class)]
class PhoneTest extends TypeTestCase
{
    /** @var string The canonical normalized US phone value. */
    private const string NORMALIZED_US_PHONE = '+16502530000';

    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'us phone in international format normalizes' => ['+1 650-253-0000', self::NORMALIZED_US_PHONE],
            'us phone in parenthesized format normalizes' => ['(650) 253-0000', self::NORMALIZED_US_PHONE],
            'us phone with spaces normalizes'             => ['1 650 253 0000', self::NORMALIZED_US_PHONE],
            'invalid phone returns null'                  => ['invalid phone', null],
            'empty string returns null'                   => ['', null],
            'spaces only returns null'                    => ['   ', null],
            'null input returns null'                     => [null, null],
            'international number remains international'  => ['+44 20 7031 3000', '+442070313000'],
            'uk number with country context normalizes'   => ['020 7031 3000', '+442070313000', 'GB'],
            'compact us phone normalizes'                 => ['16502530000', self::NORMALIZED_US_PHONE],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    protected function getNormalizerName(): string
    {
        return 'phone';
    }
}
