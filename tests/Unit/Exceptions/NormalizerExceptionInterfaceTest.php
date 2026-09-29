<?php

declare(strict_types = 1);

namespace Tests\Unit\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException;
use SineMacula\Foundation\Normalizers\Exceptions\NormalizerExceptionInterface;
use SineMacula\Foundation\Normalizers\Normalizer;
use Tests\Unit\UnitTestCase;

/**
 * Deprecated normalizer exception interface test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Normalizer::class)]
final class NormalizerExceptionInterfaceTest extends UnitTestCase
{
    /**
     * Test that the deprecated location still catches package exceptions.
     *
     * Runs in a separate process so nothing has loaded the deprecated interface
     * before the catch block names it.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testDeprecatedLocationStillCatchesPackageExceptions(): void
    {
        $caught = null;

        try {
            Normalizer::register('invalid', \stdClass::class);
        } catch (NormalizerExceptionInterface $exception) { // @phpstan-ignore catch.deprecatedInterface
            $caught = $exception;
        }

        self::assertInstanceOf(InvalidNormalizerException::class, $caught);
    }
}
