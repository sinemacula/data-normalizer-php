<?php

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The company name normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class CompanyName implements NormalizerInterface
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

        $patterns = [
            '/\binc\b\.?/i',
            '/\bllc\b\.?/i',
            '/\bltd\b\.?/i',
            '/\bgmbh\b\.?/i',
            '/\bs\.?a\.?r\.?l\.?$/i',
        ];

        $replacements = ['Inc', 'LLC', 'Ltd', 'GmbH', 'SARL'];

        return $value ? preg_replace($patterns, $replacements, $value) : null;
    }
}
