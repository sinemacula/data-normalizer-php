<?php

declare(strict_types = 1);

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Concerns\LoadsJsonResources;

/**
 * Resource provider fixture.
 *
 * Exposes the trait's private loader so tests can exercise both the happy path
 * and the missing-file path.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class ResourceProvider
{
    use LoadsJsonResources;

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
