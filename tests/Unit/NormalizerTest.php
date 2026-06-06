<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException;
use SineMacula\Foundation\Normalizers\Normalizer;
use Tests\Fixtures\ReverseNormalizer;
use Tests\Fixtures\UppercaseNormalizer;

/**
 * Normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Normalizer::class)]
class NormalizerTest extends UnitTestCase
{
    /**
     * Set up the test case.
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Normalizer::flush();
    }

    /**
     * Tear down the test case.
     *
     * @return void
     */
    #[\Override]
    protected function tearDown(): void
    {
        Normalizer::flush();

        parent::tearDown();
    }

    /**
     * Test that calls dispatch to the matching built-in normalizer.
     *
     * @return void
     */
    public function testDispatchesCallToMatchingBuiltInNormalizer(): void
    {
        static::assertSame('some string with extra spaces', Normalizer::clean(' some  string with extra    spaces '));
    }

    /**
     * Test that a registered normalizer resolves and dispatches.
     *
     * @return void
     */
    public function testRegisteredNormalizerResolvesAndDispatches(): void
    {
        Normalizer::register('uppercase', UppercaseNormalizer::class);

        static::assertSame('HELLO', Normalizer::__callStatic('uppercase', ['hello']));
    }

    /**
     * Test that a registered normalizer overrides a built-in.
     *
     * @return void
     */
    public function testRegisteredNormalizerOverridesBuiltIn(): void
    {
        Normalizer::register('clean', UppercaseNormalizer::class);

        static::assertSame(' HELLO  WORLD ', Normalizer::clean(' hello  world '));
    }

    /**
     * Test that registering after a built-in is memoised still overrides it.
     *
     * @return void
     */
    public function testRegisteringAfterBuiltInIsMemoisedStillOverridesIt(): void
    {
        static::assertSame('hello world', Normalizer::clean(' hello  world '));

        Normalizer::register('clean', UppercaseNormalizer::class);

        static::assertSame(' HELLO  WORLD ', Normalizer::clean(' hello  world '));
    }

    /**
     * Test that re-registration overwrites a prior registration.
     *
     * @return void
     */
    public function testReRegistrationOverwritesPriorRegistration(): void
    {
        Normalizer::register('custom', UppercaseNormalizer::class);
        Normalizer::register('custom', ReverseNormalizer::class);

        static::assertSame('olleh', Normalizer::__callStatic('custom', ['hello']));
    }

    /**
     * Test that registration keys are first-character case-insensitive.
     *
     * @return void
     */
    public function testRegistrationKeysAreFirstCharacterCaseInsensitive(): void
    {
        Normalizer::register('Uppercase', UppercaseNormalizer::class);

        static::assertSame('HELLO', Normalizer::__callStatic('uppercase', ['hello']));
    }

    /**
     * Test that an exception is thrown when registering a class without the
     * contract.
     *
     * @return void
     */
    public function testThrowsExceptionWhenRegisteringClassWithoutContract(): void
    {
        $this->expectException(InvalidNormalizerException::class);
        $this->expectExceptionMessage('Normalizer \'stdClass\' must implement SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface.');

        Normalizer::register('invalid', \stdClass::class);
    }

    /**
     * Test that an exception is thrown when registering a non-existent class.
     *
     * @return void
     */
    public function testThrowsExceptionWhenRegisteringNonExistentClass(): void
    {
        $this->expectException(InvalidNormalizerException::class);
        $this->expectExceptionMessage('Normalizer \'Tests\Fixtures\DoesNotExist\' must implement SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface.');

        // @phpstan-ignore argument.type (deliberately violates the class-string contract at runtime)
        Normalizer::register('missing', 'Tests\Fixtures\DoesNotExist');
    }

    /**
     * Test that flush restores built-in behaviour.
     *
     * @return void
     */
    public function testFlushRestoresBuiltInBehaviour(): void
    {
        Normalizer::register('clean', UppercaseNormalizer::class);

        Normalizer::flush();

        static::assertSame('hello world', Normalizer::clean(' hello  world '));
    }

    /**
     * Test that flush removes custom registrations.
     *
     * @return void
     */
    public function testFlushRemovesCustomRegistrations(): void
    {
        Normalizer::register('uppercase', UppercaseNormalizer::class);

        Normalizer::flush();

        $this->expectException(InvalidNormalizerException::class);

        Normalizer::__callStatic('uppercase', ['hello']);
    }

    /**
     * Test that an exception is thrown with the computed class name when the
     * normalizer is unknown.
     *
     * @return void
     */
    public function testThrowsExceptionWithComputedClassNameWhenNormalizerUnknown(): void
    {
        $this->expectException(InvalidNormalizerException::class);
        $this->expectExceptionMessage('Normalizer \'SineMacula\Foundation\Normalizers\Types\NonexistentThing\' not found.');

        Normalizer::__callStatic('nonexistentThing', ['value']);
    }
}
