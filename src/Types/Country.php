<?php

namespace SineMacula\Foundation\Normalizers\Types;

use CommerceGuys\Addressing\Country\CountryRepository;
use FuzzyWuzzy\Process;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The country normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class Country implements NormalizerInterface
{
    /** @var int The minimum length required for fuzzy matching. */
    private const int MINIMUM_FUZZY_INPUT_LENGTH = 3;

    /** @var int The minimum score required for a fuzzy match. */
    private const int MINIMUM_FUZZY_MATCH_SCORE = 70;

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

        if (!$value) {
            return null;
        }

        $value     = strtoupper($value);
        $countries = (new CountryRepository)->getList();

        if (self::isExactCountryCode($value, $countries)) {
            return $value;
        }

        if ($countryCode = self::findCountryByName($value, $countries)) {
            return $countryCode;
        }

        return self::findCountryByFuzzyMatch($value, $countries);
    }

    /**
     * Check if the value is an exact country code.
     *
     * @param  string  $value
     * @param  array<string, string>  $countries
     * @return bool
     */
    private static function isExactCountryCode(string $value, array $countries): bool
    {
        return isset($countries[$value]);
    }

    /**
     * Find the country code by matching the country name.
     *
     * @param  string  $value
     * @param  array<string, string>  $countries
     * @return ?string
     */
    private static function findCountryByName(string $value, array $countries): ?string
    {
        foreach ($countries as $code => $name) {
            if (strcasecmp($value, $name) === 0) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Find the country code by fuzzy matching the country name.
     *
     * @param  string  $value
     * @param  array<string, string>  $countries
     * @return ?string
     */
    private static function findCountryByFuzzyMatch(string $value, array $countries): ?string
    {
        $inputLength = strlen(str_replace(' ', '', $value));

        if ($inputLength < self::MINIMUM_FUZZY_INPUT_LENGTH) {
            return null;
        }

        $fuzzy = new Process;
        $match = $fuzzy->extractOne($value, $countries, null, null, self::MINIMUM_FUZZY_MATCH_SCORE);

        if (!isset($match[0]) || !is_string($match[0])) {
            return null;
        }

        $countryCode = array_search($match[0], $countries, true);

        return is_string($countryCode) ? $countryCode : null;
    }
}
