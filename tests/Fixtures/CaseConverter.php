<?php

declare(strict_types = 1);

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Concerns\ConvertsCase;

/**
 * Case converter fixture.
 *
 * Exposes the trait's private helpers so tests can exercise both the multibyte
 * and the byte-wise paths.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class CaseConverter
{
    use ConvertsCase;

    /**
     * Determine whether the value is converted with mbstring.
     *
     * @param  string  $value
     * @return bool
     */
    public static function canConvertMultibyte(string $value): bool
    {
        return self::isMultibyte($value);
    }

    /**
     * Convert the value to lowercase.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return self::toLower($value, self::isMultibyte($value));
    }

    /**
     * Uppercase the first letter of the value.
     *
     * @param  string  $value
     * @return string
     */
    public static function first(string $value): string
    {
        return self::upperFirst($value, self::isMultibyte($value));
    }
}
