<?php

namespace Tests\Unit\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException;
use SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException;
use SineMacula\Foundation\Normalizers\Traits\LoadsJsonResources;
use Tests\Fixtures\ResourceProvider;
use Tests\Unit\UnitTestCase;

/**
 * Loads JSON resources trait test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversTrait(LoadsJsonResources::class)]
class LoadsJsonResourcesTest extends UnitTestCase
{
    /**
     * Test that an existing resource file loads.
     *
     * @return void
     */
    public function testLoadsExistingResourceFile(): void
    {
        static::assertGreaterThan(1, count(ResourceProvider::load('acronyms')));
    }

    /**
     * Test that a missing resource file throws.
     *
     * @return void
     */
    public function testThrowsWhenResourceFileIsMissing(): void
    {
        $this->expectException(ResourceFileNotFoundException::class);
        $this->expectExceptionMessage('File missing.json not found.');

        ResourceProvider::load('missing');
    }

    /**
     * Test that a resource file without a JSON array throws.
     *
     * @return void
     */
    public function testThrowsWhenResourceFileIsInvalid(): void
    {
        $path = __DIR__ . '/../../../resources/clean-test-malformed.json';

        file_put_contents($path, '"not an array"');

        try {

            $this->expectException(InvalidResourceFileException::class);

            ResourceProvider::load('clean-test-malformed');
        } finally {
            unlink($path);
        }
    }

    /**
     * Test that a filename containing a forward slash throws.
     *
     * @return void
     */
    public function testThrowsWhenResourceFileNameContainsForwardSlash(): void
    {
        $this->expectException(InvalidResourceFileException::class);

        ResourceProvider::load('sub/dir');
    }

    /**
     * Test that a filename containing a backslash throws.
     *
     * @return void
     */
    public function testThrowsWhenResourceFileNameContainsBackslash(): void
    {
        $this->expectException(InvalidResourceFileException::class);

        ResourceProvider::load('sub\dir');
    }

    /**
     * Test that a filename containing a traversal sequence throws.
     *
     * @return void
     */
    public function testThrowsWhenResourceFileNameContainsTraversal(): void
    {
        $this->expectException(InvalidResourceFileException::class);

        ResourceProvider::load('..foo');
    }
}
