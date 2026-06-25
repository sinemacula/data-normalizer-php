<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Concerns\AcronymProvider;
use SineMacula\Foundation\Normalizers\Concerns\StopWordProvider;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The job title normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
 */
class JobTitle implements NormalizerInterface
{
    use AcronymProvider, StopWordProvider;

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        if (!$value) {
            return null;
        }

        $acronyms  = self::getAcronyms();
        $stopWords = self::getStopWords();
        $parts     = self::splitValue($value);

        $normalizedParts = array_map(
            static fn (string $part): string => self::normalizePart($part, $acronyms, $stopWords),
            $parts,
        );

        return implode('', $normalizedParts);
    }

    /**
     * Split the value into parts.
     *
     * @param  string  $value
     * @return array<int, string>
     */
    private static function splitValue(string $value): array
    {
        $parts = preg_split('/(\s+|[()\[\]{}])/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        return is_array($parts) ? $parts : [];
    }

    /**
     * Normalize an individual part.
     *
     * @param  string  $part
     * @param  array<int, string>  $acronyms
     * @param  array<int, string>  $stopWords
     * @return string
     */
    private static function normalizePart(string $part, array $acronyms, array $stopWords): string
    {
        if (str_contains($part, '-')) {
            return implode('-', array_map('ucfirst', explode('-', $part)));
        }

        $lowercaseAcronyms = array_map('strtolower', $acronyms);
        $acronymKey        = array_search($part, $lowercaseAcronyms, true);

        if ($acronymKey !== false) {
            return $acronyms[$acronymKey];
        }

        return in_array($part, $stopWords, true) ? $part : ucfirst($part);
    }
}
