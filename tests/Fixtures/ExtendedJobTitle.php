<?php

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Types\JobTitle;

/**
 * Extended job title fixture.
 *
 * Exposes the protected provider methods so tests can pin their subclass
 * visibility and memoisation behaviour.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class ExtendedJobTitle extends JobTitle
{
    /**
     * Expose the acronyms provider.
     *
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    public static function getExposedAcronyms(): array
    {
        return self::getAcronyms();
    }

    /**
     * Expose the stop words provider.
     *
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    public static function getExposedStopWords(): array
    {
        return self::getStopWords();
    }
}
