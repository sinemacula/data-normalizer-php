<?php

declare(strict_types = 1);

namespace Tests\Fixtures;

use SineMacula\Foundation\Normalizers\Traits\AcronymProvider;
use SineMacula\Foundation\Normalizers\Traits\StopWordProvider;

/**
 * Provider fixture pinning the deprecated Traits provider locations.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class LegacyJobTitle
{
    use AcronymProvider, StopWordProvider; // @phpstan-ignore traitUse.deprecatedTrait, traitUse.deprecatedTrait

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
