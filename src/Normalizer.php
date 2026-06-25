<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException;

/**
 * The Sine Macula data normalizer.
 *
 * Provide helper methods to normalize data.
 *
 * @method static string|null clean(string $value)
 * @method static string|null name(string $value)
 * @method static string|null email(string $value)
 * @method static string|null phone(string $value, ?string $country = null)
 * @method static string|null date(string $value)
 * @method static string|null timezone(string $value)
 * @method static string|null addressLine(string $value)
 * @method static string|null postalCode(string $value, ?string $country = null)
 * @method static string|null country(string $value)
 * @method static string|null administrativeArea(string $value, ?string $country = null)
 * @method static string|null companyName(string $value)
 * @method static string|null jobTitle(string $value)
 * @method static string|null currency(string $value)
 * @method static string|null ssn(string $value)
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @managed-static
 */
final class Normalizer
{
    /** @var array<string, class-string<\SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface>> */
    private static array $normalizers = [];

    /**
     * Route the normalize call to the relevant normalizer.
     *
     * Returns mixed because each normalizer defines its own return shape.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $arguments
     * @return mixed
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    public static function __callStatic(string $method, #[\SensitiveParameter] array $arguments): mixed
    {
        return call_user_func([self::resolve($method), 'normalize'], ...$arguments);
    }

    /**
     * Register a custom normalizer under the given method name.
     *
     * @param  string  $name
     * @param  class-string  $class
     * @return void
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException
     */
    public static function register(string $name, string $class): void
    {
        if (!is_subclass_of($class, NormalizerInterface::class)) {
            throw new InvalidNormalizerException("Normalizer '{$class}' must implement " . NormalizerInterface::class . '.');
        }

        self::$normalizers[ucfirst($name)] = $class;
    }

    /**
     * Clear all registered normalizers and memoised resolutions.
     *
     * Intended for test isolation only.
     *
     * @return void
     */
    public static function flush(): void
    {
        self::$normalizers = [];
    }

    /**
     * Resolve the given method name to a normalizer class.
     *
     * @param  string  $method
     * @return class-string<\SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface>
     *
     * @throws \SineMacula\Foundation\Normalizers\Exceptions\InvalidNormalizerException
     */
    private static function resolve(string $method): string
    {
        $key = ucfirst($method);

        if (isset(self::$normalizers[$key])) {
            return self::$normalizers[$key];
        }

        $normalizer = 'SineMacula\Foundation\Normalizers\Types\\' . $key;

        if (is_subclass_of($normalizer, NormalizerInterface::class)) {
            return self::$normalizers[$key] = $normalizer;
        }

        throw new InvalidNormalizerException("Normalizer '{$normalizer}' not found.");
    }
}
