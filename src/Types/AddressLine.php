<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Concerns\ConvertsCase;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The address line normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @inheritable
 */
class AddressLine implements NormalizerInterface
{
    use ConvertsCase;

    /** @var string The byte-wise pattern matching each word of the line. */
    private const string WORD_PATTERN = '/\b\w+\'?\w*\b/';

    /** @var string The Unicode-aware pattern matching each word of the line. */
    private const string MULTIBYTE_WORD_PATTERN = '/(?<![\p{L}\p{M}\p{N}_])[\p{L}\p{M}\p{N}_]+(?:\'[\p{L}\p{M}\p{N}_]+)?/u';

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

        if ($value === null) {
            return null;
        }

        $multibyte = self::isMultibyte($value);

        $normalized = preg_replace_callback(
            $multibyte ? self::MULTIBYTE_WORD_PATTERN : self::WORD_PATTERN,
            static fn (array $matches): string => self::upperFirst(self::toLower($matches[0], $multibyte), $multibyte),
            $value,
        );

        $normalized = rtrim((string) preg_replace('/,+\s*$/', '', (string) $normalized));

        return $normalized !== '' ? $normalized : null;
    }
}
