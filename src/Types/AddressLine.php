<?php

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The address line normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class AddressLine implements NormalizerInterface
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

        if ($value === null || $value === '') {
            return null;
        }

        $normalized = preg_replace_callback('/\b\w+\'?\w*\b/', static fn (array $matches): string => ucfirst(strtolower($matches[0])), $value);

        $normalized = preg_replace('/,+\s*$/', '', (string) $normalized);
        $normalized = rtrim($normalized);

        return $normalized !== '' ? $normalized : null;
    }
}
