<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Exceptions;

use SineMacula\Foundation\Normalizers\Exceptions\Contracts\NormalizerExceptionInterface;

/**
 * Exception thrown when a normalizer cannot be resolved or registered.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class InvalidNormalizerException extends \InvalidArgumentException implements NormalizerExceptionInterface {}
