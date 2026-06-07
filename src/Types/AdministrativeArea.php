<?php

namespace SineMacula\Foundation\Normalizers\Types;

use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The administrative area normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class AdministrativeArea implements NormalizerInterface
{
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

        return self::findMatchingSubdivision($value, self::getSubdivisions($country));
    }

    /**
     * Get the country code from the given context.
     *
     * @param  mixed|null  $context
     * @return string
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
