<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Concerns;

/**
 * The stop word provider trait.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
trait StopWordProvider
{
    use LoadsJsonResources;

    /** @var array<int, string> */
    private static array $stopWords;

    /**
     * Return the stop words.
     *
     * @return array<int, string>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException
     */
    protected static function getStopWords(): array
    {
        return self::$stopWords ??= self::loadJson('stopwords');
    }
}
