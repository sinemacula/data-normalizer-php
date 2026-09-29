<?php

declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidResourceFileException;
use SineMacula\Foundation\Normalizers\Exceptions\ResourceFileNotFoundException;
use SineMacula\Foundation\Normalizers\Normalizer;
use SineMacula\Foundation\Normalizers\Types\AddressLine;
use SineMacula\Foundation\Normalizers\Types\AdministrativeArea;
use SineMacula\Foundation\Normalizers\Types\Clean;
use SineMacula\Foundation\Normalizers\Types\CompanyName;
use SineMacula\Foundation\Normalizers\Types\Country;
use SineMacula\Foundation\Normalizers\Types\Currency;
use SineMacula\Foundation\Normalizers\Types\Date;
use SineMacula\Foundation\Normalizers\Types\Email;
use SineMacula\Foundation\Normalizers\Types\JobTitle;
use SineMacula\Foundation\Normalizers\Types\Name;
use SineMacula\Foundation\Normalizers\Types\Phone;
use SineMacula\Foundation\Normalizers\Types\PostalCode;
use SineMacula\Foundation\Normalizers\Types\Ssn;
use SineMacula\Foundation\Normalizers\Types\Timezone;

/**
 * Published class extensibility test.
 *
 * Every class published as non-final in 1.0 stays extensible until 2.0.0, so
 * consumer subclasses keep loading.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversNothing]
final class ExtensibilityTest extends UnitTestCase
{
    /**
     * Published class data provider.
     *
     * @return iterable<string, array{class-string}>
     */
    public static function publishedClassProvider(): iterable
    {
        $classes = [
            Normalizer::class,
            AddressLine::class,
            AdministrativeArea::class,
            Clean::class,
            CompanyName::class,
            Country::class,
            Currency::class,
            Date::class,
            Email::class,
            JobTitle::class,
            Name::class,
            Phone::class,
            PostalCode::class,
            Ssn::class,
            Timezone::class,
            InvalidNormalizerException::class,
            InvalidResourceFileException::class,
            ResourceFileNotFoundException::class,
        ];

        foreach ($classes as $class) {
            yield $class => [$class];
        }
    }

    /**
     * Test that the published class is not final.
     *
     * @param  class-string  $class
     * @return void
     */
    #[DataProvider('publishedClassProvider')]
    public function testPublishedClassIsNotFinal(string $class): void
    {
        self::assertFalse((new \ReflectionClass($class))->isFinal());
    }
}
