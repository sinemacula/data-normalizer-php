<?php

namespace SineMacula\Foundation\Normalizers\Exceptions;

/**
 * Exception thrown when a configured resource file cannot be found.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class ResourceFileNotFoundException extends \RuntimeException implements NormalizerExceptionInterface {}
