<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Types;

use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The company name normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class CompanyName implements NormalizerInterface
{
    /** @var array<int, string> Legal company-suffix patterns. */
    private const array SUFFIX_PATTERNS = [
        '/\binc\b\.?/i',
        '/\bllc\b\.?/i',
        '/\bltd\b\.?/i',
        '/\bgmbh\b\.?/i',
        '/\bs\.?a\.?r\.?l\.?$/i',
    ];

    /** @var array<int, string> Replacements for SUFFIX_PATTERNS. */
    private const array SUFFIX_REPLACEMENTS = ['Inc', 'LLC', 'Ltd', 'GmbH', 'SARL'];

    /**
     * Normalize the given value.
     *
     * @param  mixed  $value
     * @param  mixed|null  $context
     * @return string|null
     */
    #[\Override]
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        $value = Normalizer::clean($value);

        return $value ? preg_replace(self::SUFFIX_PATTERNS, self::SUFFIX_REPLACEMENTS, $value) : null;
    }
}
