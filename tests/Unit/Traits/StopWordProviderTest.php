<?php

declare(strict_types = 1);

namespace Tests\Unit\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use SineMacula\Foundation\Normalizers\Concerns\StopWordProvider;
use SineMacula\Foundation\Normalizers\Types\JobTitle;
use Tests\Fixtures\ExtendedJobTitle;
use Tests\Unit\UnitTestCase;

/**
 * Stop word provider trait test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversTrait(StopWordProvider::class)]
final class StopWordProviderTest extends UnitTestCase
{
    /**
     * Test that the provider is accessible from subclasses.
     *
     * @return void
     */
    public function testProviderIsAccessibleFromSubclasses(): void
    {
        self::assertNotEmpty(ExtendedJobTitle::getExposedStopWords());
    }

    /**
     * Test that the provider memoises the loaded stop words.
     *
     * The reflection coupling is deliberate: memoisation is unobservable
     * through the public surface, and this test pins it for mutation testing.
     *
     * @return void
     */
    public function testProviderMemoisesLoadedStopWords(): void
    {
        $stopWords = new \ReflectionProperty(JobTitle::class, 'stopWords');

        $originalStopWords = ExtendedJobTitle::getExposedStopWords();

        try {

            $stopWords->setValue(null, ['__sentinel__']);

            self::assertSame(['__sentinel__'], ExtendedJobTitle::getExposedStopWords());
        } finally {
            $stopWords->setValue(null, $originalStopWords);
        }
    }
}
