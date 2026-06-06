<?php

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;

/**
 * The financial amount normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class FinancialAmount implements NormalizerInterface
{
    /** @var int Minor-unit multiplier; assumes a two-decimal currency. */
    private const int MINOR_UNIT_MULTIPLIER = 100;

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
        return is_numeric($value) ? (int) round($value * self::MINOR_UNIT_MULTIPLIER) : null;
    }
}
