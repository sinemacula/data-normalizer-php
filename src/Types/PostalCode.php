<?php

namespace SineMacula\Foundation\Normalizers\Types;

use Brick\Postcode\Exception\InvalidPostcodeException;
use Brick\Postcode\Exception\UnknownCountryException;
use Brick\Postcode\PostcodeFormatter;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The postal code normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class PostalCode implements NormalizerInterface
{
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

        $country = Normalizer::country($context);

        return $country === null ? strtoupper($value) : self::formatForCountry($country, $value);
    }

    /**
     * Format the value against the given country's postal code rules.
     *
     * @param  string  $country
     * @param  string  $value
     * @return string|null
     */
    private static function formatForCountry(string $country, string $value): ?string
    {
        try {
            return (new PostcodeFormatter)->format($country, $value);
        } catch (InvalidPostcodeException) {
            return null;
        } catch (UnknownCountryException) {
            return strtoupper($value);
        }
    }
}
