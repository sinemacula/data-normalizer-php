<?php

namespace SineMacula\Foundation\Normalizers\Traits;

use SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException;
use SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException;

/**
 * The loads JSON resources trait.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
trait LoadsJsonResources
{
    /**
     * Load the data from JSON.
     *
     * @param  string  $filename
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    private static function loadJson(string $filename): array
    {
        if (preg_match('/^[A-Za-z0-9_-]+$/', $filename) !== 1) {
            throw new InvalidResourceFileException("File {$filename}.json is not a valid resource file name.");
        }

        $path = __DIR__ . '/../../resources/' . $filename . '.json';

        if (!file_exists($path)) {
            throw new ResourceFileNotFoundException("File {$filename}.json not found.");
        }

        $entries = json_decode((string) file_get_contents($path), true);

        if (!is_array($entries)) {
            throw new InvalidResourceFileException("File {$filename}.json does not contain a valid JSON array.");
        }

        /** @var array<int, string> $entries */
        return $entries;
    }
}
