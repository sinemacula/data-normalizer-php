<?php

declare(strict_types = 1);

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * Facade subclass fixture carrying docblocks for a registered normalizer.
 *
 * Mirrors the README pattern for IDE completion of custom normalizers.
 *
 * @method static string|null uppercase(string $value)
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class DocumentedNormalizer extends Normalizer {}
