<?php

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The SSN normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class Ssn implements NormalizerInterface
{
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

        if ($value === null) {
            return null;
        }

        // Redacted SSNs are preserved verbatim; stripping to digits would fabricate a partial SSN.
        if (preg_match('/^\*+\d+$/', $value)) {
            return $value;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits !== '' ? $digits : null;
    }
}
