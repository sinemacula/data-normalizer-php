<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;

/**
 * The financial amount normalizer.
 *
 * Converts a major-unit amount (e.g. 12.34) to an integer count of minor units
 * (e.g. 1234), rounding half away from zero.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
 */
class FinancialAmount implements NormalizerInterface
{
    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return int|null
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?int
    {
        if (!is_int($value) && !is_float($value) && !(is_string($value) && is_numeric($value))) {
            return null;
        }

        $amount = round((float) $value * 100);

        if (!is_finite($amount) || abs($amount) >= PHP_INT_MAX) {
            return null;
        }

        return (int) $amount;
    }
}
