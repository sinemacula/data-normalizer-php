<?php

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;

/**
 * Uppercase normalizer fixture.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UppercaseNormalizer implements NormalizerInterface
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
        return is_string($value) ? strtoupper($value) : null;
    }
}
