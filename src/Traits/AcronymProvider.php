<?php

namespace SineMacula\Foundation\Normalizers\Traits;

/**
 * The acronym provider trait.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
trait AcronymProvider
{
    use LoadsJsonResources;

    /** @var array<int, string> */
    private static array $acronyms;

    /**
     * Return the acronyms.
     *
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    protected static function getAcronyms(): array
    {
        return self::$acronyms ??= self::loadJson('acronyms');
    }
}
