<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Concerns\ConvertsCase;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The email normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
 */
class Email implements NormalizerInterface
{
    use ConvertsCase;

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     */
    #[\Override]
    public static function normalize(#[\SensitiveParameter] mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        return $value ? self::lowercase(str_replace(' ', '', $value)) : null;
    }

    /**
     * Lowercase the address.
     *
     * Non-ASCII letters are lowercased only when their lowercase form is a
     * single non-ASCII letter, so compatibility characters such as the Kelvin
     * sign never collapse into a different ASCII address.
     *
     * @param  string  $value
     * @return string
     */
    private static function lowercase(string $value): string
    {
        $value = strtolower($value);

        if (!self::isMultibyte($value)) {
            return $value;
        }

        return (string) preg_replace_callback('/[^\x00-\x7F]/u', static function (array $matches): string {

            $lowercase = self::toLower($matches[0], true);

            return preg_match('/[\x00-\x7F]/', $lowercase) === 1 ? $matches[0] : $lowercase;
        }, $value);
    }
}
