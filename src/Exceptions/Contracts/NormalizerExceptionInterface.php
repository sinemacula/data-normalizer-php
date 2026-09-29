<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Exceptions\Contracts;

use SineMacula\Foundation\Normalizers\Exceptions\NormalizerExceptionInterface as LegacyNormalizerExceptionInterface;

/**
 * Marker contract for all exceptions thrown by the normalizer package.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
interface NormalizerExceptionInterface extends LegacyNormalizerExceptionInterface // @phpstan-ignore interface.extendsDeprecatedInterface
{
    // Extends the deprecated location so catch blocks naming it still match;
    // the parent is dropped in 2.0.0.
}
