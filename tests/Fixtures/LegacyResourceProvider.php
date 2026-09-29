<?php

declare(strict_types = 1);

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Traits\LoadsJsonResources;

/**
 * Resource provider fixture pinning the deprecated Traits loader location.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class LegacyResourceProvider
{
    use LoadsJsonResources; // @phpstan-ignore traitUse.deprecatedTrait

    /**
     * Load the given resource file.
     *
     * @param  string  $filename
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    public static function load(string $filename): array
    {
        return self::loadJson($filename);
    }
}
