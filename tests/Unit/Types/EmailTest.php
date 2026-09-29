<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Email;

/**
 * Email normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Email::class)]
final class EmailTest extends TypeTestCase
{
    /** @var string The canonical normalized email value. */
    private const string NORMALIZED_EMAIL = 'test@example.com';

    /**
     * Data provider for test cases.
     *
     * @return array<string, array<int, mixed>>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[\Override]
    public static function dataProvider(): array
    {
        return [
            'uppercase email is lowercased'                => ['TEST@EXAMPLE.COM', self::NORMALIZED_EMAIL],
            'trimmed email normalizes'                     => ['  test@example.com  ', self::NORMALIZED_EMAIL],
            'already normalized email unchanged'           => [self::NORMALIZED_EMAIL, self::NORMALIZED_EMAIL],
            'mixed case email is normalized'               => ['TeSt@ExAmPlE.CoM', self::NORMALIZED_EMAIL],
            'email with internal spaces is normalized'     => [' test @ example . com ', self::NORMALIZED_EMAIL],
            'empty string returns null'                    => ['', null],
            'only spaces returns null'                     => ['   ', null],
            'null input returns null'                      => [null, null],
            'invalid email format remains normalized text' => ['invalid-email-format', 'invalid-email-format'],
            'accented letters are lowercased'              => ['JOSÉ@EXAMPLE.COM', 'josé@example.com'],
            'kelvin sign is not folded to ascii'           => ["ADMIN\u{212A}@EXAMPLE.COM", "admin\u{212A}@example.com"],
            'dotted capital i is kept'                     => ["\u{0130}PEK@EXAMPLE.COM", "\u{0130}pek@example.com"],
            'invalid utf-8 keeps byte-wise casing'         => ["J\xC9SUS@EXAMPLE.COM", "j\xC9sus@example.com"],
        ];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'email';
    }
}
