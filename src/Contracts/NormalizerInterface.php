<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Contracts;

/**
 * Normalizer interface.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
interface NormalizerInterface
{
    /**
     * Normalize the given value.
     *
     * The contract is deliberately `mixed`: normalizers accept raw input of
     * any shape, return null for values they cannot normalize, and each
     * implementation defines its own normalized return shape.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return mixed
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    public static function normalize(mixed $value, mixed $context = null): mixed;
}
