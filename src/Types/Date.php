<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The date normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
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

    /** @var array<string, int> The relative part of a date with no offset. */
    private const array NO_RELATIVE_OFFSET = [
        'year'   => 0,
        'month'  => 0,
        'day'    => 0,
        'hour'   => 0,
        'minute' => 0,
        'second' => 0,
    ];

    /**
     * Normalize the given value.
     *
     * Pass ['relative' => false] as the context to accept only absolute
     * dates; relative expressions and timestamps then return null.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        if ($value === null) {
            return null;
        }

        $date = self::parseDate($value, self::allowsRelative($context));

        return $date?->format('Y-m-d');
    }

    /**
     * Determine whether the context allows relative dates.
     *
     * Only ['relative' => false] disables them, so any other context keeps the
     * default behaviour.
     *
     * @param  mixed|null  $context
     * @return bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function allowsRelative(mixed $context): bool
    {
        return !is_array($context) || ($context['relative'] ?? true) !== false;
    }

    /**
     * Parse the given value using strict supported formats.
     *
     * @param  string  $value
     * @param  bool  $allowRelative
     * @return \DateTimeImmutable|null
     */
    private static function parseDate(string $value, bool $allowRelative): ?\DateTimeImmutable
    {
        $date = self::parseUsingSupportedFormats($value);

        if ($date !== null) {
            return $date;
        }

        // Absolute dates that failed strict parsing are invalid calendar dates;
        // the native parser would roll them over.
        if (self::isAbsoluteDateExpression($value)) {
            return null;
        }

        return $allowRelative ? self::parseUsingNativeParser($value) : self::parseAbsoluteExpression($value);
    }

    /**
     * Parse the value only when it names a complete, absolute date.
     *
     * The value may not be a Unix timestamp or carry a relative part other than
     * a weekday, and the parsed date must be the year, month and day it names.
     * That rejects a missing year, invalid dates and times that roll over, and
     * a weekday that does not match the date. A month and year alone resolve to
     * the first of the month.
     *
     * @param  string  $value
     * @return \DateTimeImmutable|null
     */
    private static function parseAbsoluteExpression(string $value): ?\DateTimeImmutable
    {
        if (str_starts_with($value, '@')) {
            return null;
        }

        $parsed = date_parse($value);

        $relative = $parsed['relative'] ?? self::NO_RELATIVE_OFFSET;

        unset($relative['weekday']);

        if ($relative !== self::NO_RELATIVE_OFFSET) {
            return null;
        }

        $date = self::parseUsingNativeParser($value);

        return $date?->format('Y-m-d') === sprintf('%04d-%02d-%02d', $parsed['year'], $parsed['month'], $parsed['day']) ? $date : null;
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
