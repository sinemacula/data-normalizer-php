<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;

/**
 * The clean normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class Clean implements NormalizerInterface
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
        if (!is_string($value)) {
            return null;
        }

        $normalizedValue = preg_replace('/\s+/', ' ', trim($value));

        if ($normalizedValue === null || $normalizedValue === '') {
            return null;
        }

        return $normalizedValue;
    }
}
