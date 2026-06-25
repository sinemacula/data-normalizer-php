<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\CompanyName;

/**
 * Company name normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(CompanyName::class)]
final class CompanyNameTest extends TypeTestCase
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
            'normalize inc suffix'            => ['  Example Inc.  ', 'Example Inc'],
            'normalize llc suffix'            => ['  example llc  ', 'example LLC'],
            'normalize ltd suffix'            => ['  example ltd.  ', 'example Ltd'],
            'normalize gmbh suffix'           => ['example gmbh', 'example GmbH'],
            'normalize sarl dotted suffix'    => ['  example s.a.r.l.  ', 'example SARL'],
            'normalize uppercase dotted sarl' => ['Example S.A.R.L', 'Example SARL'],
            'non matching pattern unchanged'  => ['example company', 'example company'],
            'only spaces returns null'        => ['  ', null],
            'null input returns null'         => [null, null],
            'simple text unchanged'           => ['simple text', 'simple text'],
            'empty string returns null'       => ['', null],
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
        return 'companyName';
    }
}
