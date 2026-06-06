<?php

namespace SineMacula\Foundation\Normalizers\Exceptions;

/**
 * Exception thrown when a normalizer cannot be resolved or registered.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class InvalidNormalizerException extends \InvalidArgumentException implements NormalizerExceptionInterface {}
