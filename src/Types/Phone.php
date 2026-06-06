<?php

namespace SineMacula\Foundation\Normalizers\Types;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

/**
 * The phone normalizer.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class Phone implements NormalizerInterface
{
    /** @var string The region used when no country context is given. */
    private const string DEFAULT_REGION = 'US';

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

        if (!$value) {
            return null;
        }

        $utility = PhoneNumberUtil::getInstance();

        try {
            $number = $utility->parse($value, is_string($context) ? strtoupper($context) : self::DEFAULT_REGION);
        } catch (NumberParseException) {
            return null;
        }

        return $utility->isValidNumber($number) ? $utility->format($number, PhoneNumberFormat::E164) : null;
    }
}
