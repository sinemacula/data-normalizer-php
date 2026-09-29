<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Exceptions;

/**
 * Marker contract for all normalizer exceptions at its former location.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @deprecated  Use \SineMacula\Foundation\Normalizers\Exceptions\Contracts\NormalizerExceptionInterface
 *              instead; this interface is removed in 2.0.0.
 */
interface NormalizerExceptionInterface // phpcs:ignore SineMacula.Namespaces.RequireContractsNamespace.NotInContracts
{
    // Marker only; kept so catch blocks naming this location still match.
}
