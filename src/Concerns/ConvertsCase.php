<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Concerns;

/**
 * Converts letter case with Unicode awareness.
 *
 * Valid UTF-8 is converted with mbstring, which symfony/polyfill-mbstring
 * provides through giggsey/libphonenumber-for-php when the extension is
 * missing. Anything else, or any install without mbstring at all, falls back to
 * the byte-wise functions so existing output for such input is unchanged.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
trait ConvertsCase
{
    /** @var string The lowercase form mbstring gives the dotted capital I. */
    private static string $lowercaseDottedI = "i\u{0307}";

    /**
     * Determine whether the value can be converted with mbstring.
     *
     * @param  string  $value
     * @return bool
     */
    private static function isMultibyte(string $value): bool
    {
        return function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8');
    }

    /**
     * Convert the value to lowercase.
     *
     * @param  string  $value
     * @param  bool  $multibyte
     * @return string
     */
    private static function toLower(string $value, bool $multibyte): string
    {
        return $multibyte ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }

    /**
     * Uppercase the first letter of the value.
     *
     * A leading dotted capital I that toLower() decomposed is restored, any
     * other decomposed one reads as a plain i, and a first letter whose title
     * case is more than one letter (such as ß) is kept as it is.
     *
     * @param  string  $value
     * @param  bool  $multibyte
     * @return string
     */
    private static function upperFirst(string $value, bool $multibyte): string
    {
        if (!$multibyte) {
            return ucfirst($value);
        }

        if (str_starts_with($value, self::$lowercaseDottedI)) {
            return "\u{0130}" . str_replace(self::$lowercaseDottedI, 'i', substr($value, strlen(self::$lowercaseDottedI)));
        }

        $first = mb_substr($value, 0, 1, 'UTF-8');
        $title = mb_convert_case($first, MB_CASE_TITLE, 'UTF-8');

        if (mb_strlen($title, 'UTF-8') !== 1) {
            $title = $first;
        }

        return $title . str_replace(self::$lowercaseDottedI, 'i', mb_substr($value, 1, null, 'UTF-8'));
    }
}
