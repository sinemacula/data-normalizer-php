<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Ssn;

/**
 * SSN normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Ssn::class)]
final class SsnTest extends TypeTestCase
{
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
            'dashed ssn normalizes to digits'       => ['123-45-6789', '123456789'],
            'spaced ssn normalizes to digits'       => ['123 45 6789', '123456789'],
            'plain digits remain unchanged'         => ['123456789', '123456789'],
            'ssn with dots normalizes to digits'    => ['123.45.6789', '123456789'],
            'ssn with mixed separators normalizes'  => ['123-45 6789', '123456789'],
            'empty string returns null'             => ['', null],
            'spaces only returns null'              => ['   ', null],
            'null input returns null'               => [null, null],
            'non-digit string returns null'         => ['abcdefghi', null],
            'mixed alpha and digits keeps digits'   => ['SSN: 123-45-6789', '123456789'],
            'redacted ssn is returned as-is'        => ['******123', '******123'],
            'leading alpha with mask keeps digits'  => ['abc***123', '123'],
            'trailing alpha with mask keeps digits' => ['***123abc', '123'],
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
        return 'ssn';
    }
}
