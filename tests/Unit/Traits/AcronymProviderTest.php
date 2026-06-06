<?php

namespace Tests\Unit\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use SineMacula\Foundation\Normalizers\Traits\AcronymProvider;
use SineMacula\Foundation\Normalizers\Types\JobTitle;
use Tests\Fixtures\ExtendedJobTitle;
use Tests\Unit\UnitTestCase;

/**
 * Acronym provider trait test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversTrait(AcronymProvider::class)]
class AcronymProviderTest extends UnitTestCase
{
    /**
     * Test that the provider is accessible from subclasses.
     *
     * @return void
     */
    public function testProviderIsAccessibleFromSubclasses(): void
    {
        static::assertNotEmpty(ExtendedJobTitle::getExposedAcronyms());
    }

    /**
     * Test that the provider memoises the loaded acronyms.
     *
     * The reflection coupling is deliberate: memoisation is unobservable
     * through the public surface, and this test pins it for mutation testing.
     *
     * @return void
     */
    public function testProviderMemoisesLoadedAcronyms(): void
    {
        $acronyms = new \ReflectionProperty(JobTitle::class, 'acronyms');

        $originalAcronyms = ExtendedJobTitle::getExposedAcronyms();

        try {

            $acronyms->setValue(null, ['__sentinel__']);

            static::assertSame(['__sentinel__'], ExtendedJobTitle::getExposedAcronyms());
        } finally {
            $acronyms->setValue(null, $originalAcronyms);
        }
    }
}
