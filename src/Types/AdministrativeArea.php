<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use SineMacula\Foundation\Normalizers\Concerns\ConvertsCase;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The administrative area normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
 */
class AdministrativeArea implements NormalizerInterface
{
    use ConvertsCase;

    /** @var string The country used when no country context is given. */
    private const string DEFAULT_COUNTRY = 'US';

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

        $country = self::getCountryFromContext($context);

        $subdivisions = self::getSubdivisions($country);

        return self::findMatchingSubdivision($value, $subdivisions) ?? self::findFoldedSubdivision($value, $subdivisions);
    }

    /**
     * Get the country code from the given context.
     *
     * @param  mixed|null  $context
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function getCountryFromContext(mixed $context = null): string
    {
        return Normalizer::country($context) ?? self::DEFAULT_COUNTRY;
    }

    /**
     * Find the matching subdivision code or name.
     *
     * @param  string  $value
     * @param  array<string, \CommerceGuys\Addressing\Subdivision\Subdivision>  $subdivisions
     * @return ?string
     */
    private static function findMatchingSubdivision(string $value, array $subdivisions): ?string
    {
        foreach ($subdivisions as $subdivision) {
            if (
                strcasecmp($value, $subdivision->getName())    === 0
                || strcasecmp($value, $subdivision->getCode()) === 0
            ) {
                return $subdivision->getCode() ?: $subdivision->getName();
            }
        }

        return null;
    }

    /**
     * Find the subdivision whose case-folded code or name matches.
     *
     * Runs only for non-ASCII input the ASCII case-insensitive match missed, so
     * every existing result is kept. Turkish dotted and dotless i fold to i.
     *
     * @param  string  $value
     * @param  array<string, \CommerceGuys\Addressing\Subdivision\Subdivision>  $subdivisions
     * @return ?string
     */
    private static function findFoldedSubdivision(string $value, array $subdivisions): ?string
    {
        if (preg_match('/[^\x00-\x7F]/', $value) !== 1 || !self::isMultibyte($value)) {
            return null;
        }

        $folded = self::fold($value);

        foreach ($subdivisions as $subdivision) {
            if (
                self::fold($subdivision->getName())    === $folded
                || self::fold($subdivision->getCode()) === $folded
            ) {
                return $subdivision->getCode() ?: $subdivision->getName();
            }
        }

        return null;
    }

    /**
     * Fold the value for a Unicode case-insensitive comparison.
     *
     * @param  string  $value
     * @return string
     */
    private static function fold(string $value): string
    {
        return str_replace("i\u{0307}", 'i', self::toLower(str_replace("\u{0131}", 'i', $value), true));
    }

    /**
     * Get all subdivisions for the given country.
     *
     * @param  string  $country
     * @return array<string, \CommerceGuys\Addressing\Subdivision\Subdivision>
     */
    private static function getSubdivisions(string $country): array
    {
        return (new SubdivisionRepository)->getAll([$country]);
    }
}
