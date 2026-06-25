<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The name normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class Name implements NormalizerInterface
{
    /** @var array<int, string> Name prefixes with special capitalization. */
    private const array SPECIAL_CASES_PREFIXES = [
        'Mc',
        'Mac',
        'O\'',
    ];

    /** @var array<int, string> Name particles kept lowercase. */
    private const array SPECIAL_CASES_LOWERCASE = [
        'van',
        'von',
        'de',
        'del',
        'della',
        'di',
        'da',
        'pietro',
        'vanden',
        'du',
        'st.',
        'st',
        'la',
        'ter',
        'den',
    ];

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     */
    #[\Override]
    public static function normalize(#[\SensitiveParameter] mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        if (!$value) {
            return null;
        }

        $value = self::normalizeCommaSeparatedName($value);

        $parts = self::splitValueIntoParts($value);

        $normalizedParts = array_map(
            fn (string $part, int $index): string => self::normalizePart($part, $index),
            $parts,
            array_keys($parts),
        );

        return implode(' ', $normalizedParts);
    }

    /**
     * Normalize a name when it contains a comma.
     *
     * @param  string  $value
     * @return string
     */
    private static function normalizeCommaSeparatedName(string $value): string
    {
        if (str_contains($value, ',')) {

            $parts = explode(',', $value);

            if (count($parts) === 2) {
                return trim($parts[1]) . ' ' . trim($parts[0]);
            }
        }

        return $value;
    }

    /**
     * Split the value into parts.
     *
     * @param  string  $value
     * @return array<int, string>
     */
    private static function splitValueIntoParts(string $value): array
    {
        return explode(' ', strtolower($value));
    }

    /**
     * Normalize an individual part of the name.
     *
     * @param  string  $part
     * @param  int  $index
     * @return string
     */
    private static function normalizePart(string $part, int $index): string
    {
        foreach (self::SPECIAL_CASES_PREFIXES as $prefix) {
            if (stripos($part, $prefix) === 0) {
                return self::applyPrefixCapitalization($part, $prefix);
            }
        }

        if (in_array($part, self::SPECIAL_CASES_LOWERCASE, true)) {
            return $index === 0 ? ucfirst($part) : $part;
        }

        return self::capitalizeName($part);
    }

    /**
     * Apply special capitalization to the given prefix.
     *
     * @param  string  $part
     * @param  string  $prefix
     * @return string
     */
    private static function applyPrefixCapitalization(string $part, string $prefix): string
    {
        $parts    = explode('-', $part);
        $parts[0] = $prefix . ucfirst(strtolower(substr($parts[0], strlen($prefix))));

        foreach ($parts as $index => $segment) {

            if ($index === 0) {
                continue;
            }

            $parts[$index] = ucfirst(strtolower($segment));
        }

        return implode('-', $parts);
    }

    /**
     * Capitalize the given name.
     *
     * @param  string  $name
     * @return string
     */
    private static function capitalizeName(string $name): string
    {
        $parts = explode('-', $name);

        $capitalizedParts = array_map(fn (string $part): string => ucfirst(strtolower($part)), $parts);

        return implode('-', $capitalizedParts);
    }
}
