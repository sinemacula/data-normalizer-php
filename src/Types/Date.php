<?php

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The date normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class Date implements NormalizerInterface
{
    /** @var array<int, string> The supported date formats. */
    private const array SUPPORTED_INPUT_FORMATS = [
        'Y-m-d',
        'Y.m.d',
        'Y/m/d',
        'm/d/Y',
        'F j, Y',
        'jS F Y',
        'j F Y',
        'M j, Y',
        'jS M Y',
        'j M Y',
    ];

    /** @var array<int, string> Patterns for absolute calendar dates. */
    private const array ABSOLUTE_DATE_PATTERNS = [
        '/^\d{4}[-.\/]\d{1,2}[-.\/]\d{1,2}$/',
        '/^\d{1,2}[-.\/]\d{1,2}[-.\/]\d{2,4}$/',
        '/^\d{1,2}(st|nd|rd|th)?\s+[a-z]+\s+\d{4}$/i',
        '/^[a-z]+\s+\d{1,2}(st|nd|rd|th)?(?:,)?\s+\d{4}$/i',
    ];

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        if ($value === null) {
            return null;
        }

        $date = self::parseDate($value);

        return $date?->format('Y-m-d');
    }

    /**
     * Parse the given value using strict supported formats.
     *
     * @param  string  $value
     * @return \DateTimeImmutable|null
     */
    private static function parseDate(string $value): ?\DateTimeImmutable
    {
        $date = self::parseUsingSupportedFormats($value);

        if ($date !== null) {
            return $date;
        }

        // Absolute dates that failed strict parsing are invalid calendar dates; the native parser would roll them over.
        if (self::isAbsoluteDateExpression($value)) {
            return null;
        }

        return self::parseUsingNativeParser($value);
    }

    /**
     * Parse the value using strict known formats only.
     *
     * @param  string  $value
     * @return \DateTimeImmutable|null
     */
    private static function parseUsingSupportedFormats(string $value): ?\DateTimeImmutable
    {
        foreach (self::SUPPORTED_INPUT_FORMATS as $format) {

            $date = \DateTimeImmutable::createFromFormat('!' . $format, $value);

            if ($date === false) {
                continue;
            }

            $errors = \DateTimeImmutable::getLastErrors();

            if (
                $errors !== false
                && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)
            ) {
                continue;
            }

            return $date;
        }

        return null;
    }

    /**
     * Determine if the value is an absolute calendar date expression.
     *
     * @param  string  $value
     * @return bool
     */
    private static function isAbsoluteDateExpression(string $value): bool
    {
        foreach (self::ABSOLUTE_DATE_PATTERNS as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse date expressions that are not absolute calendar date strings.
     *
     * @param  string  $value
     * @return \DateTimeImmutable|null
     */
    private static function parseUsingNativeParser(string $value): ?\DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\DateMalformedStringException) {
            return null;
        }
    }
}
