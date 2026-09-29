<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\AddressLine;

/**
 * Address line normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(AddressLine::class)]
final class AddressLineTest extends TypeTestCase
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
            'title case normalization'     => ['123 main st.', '123 Main St.'],
            'trim and normalize spacing'   => ['  456  Elm Street  ', '456 Elm Street'],
            'uppercase words normalized'   => ['APARTMENT 5A', 'Apartment 5a'],
            'only spaces returns null'     => ['   ', null],
            'null input returns null'      => [null, null],
            'po box casing is normalized'  => ['PO Box 123', 'Po Box 123'],
            'hyphenated street normalized' => [
                '123-martin luther king jr. blvd',
                '123-Martin Luther King Jr. Blvd',
            ],
            'apostrophes are preserved'             => ['john\'s street', 'John\'s Street'],
            'mixed case with apostrophe normalized' => ['St. JOHN\'S AVENUE', 'St. John\'s Avenue'],
            'trailing comma removed'                => ['123 main st,', '123 Main St'],
            'trailing comma with space removed'     => ['123 main st ,', '123 Main St'],
            'leading umlaut word stays whole'       => ['ÖSTERSTRASSE 5', 'Österstrasse 5'],
            'accented words are capitalized'        => ['12 élysée ave', '12 Élysée Ave'],
            'accented elided word normalizes'       => ['123 RUE DE L\'ÉGLISE', '123 Rue De L\'église'],
            'invalid utf-8 keeps byte-wise casing'  => ["12 \xC9LYSEE AVE", "12 \xC9Lysee Ave"],
        ];
    }

    /**
     * Test that a failed trailing comma strip returns null.
     *
     * Starving PCRE forces the comma strip to fail, which must return null as
     * it did before strict types rather than pass null into rtrim().
     *
     * @return void
     */
    public function testFailedTrailingCommaStripReturnsNull(): void
    {
        $jit            = ini_set('pcre.jit', '0');
        $backtrackLimit = ini_set('pcre.backtrack_limit', '2');

        try {
            self::assertNull(AddressLine::normalize('x, , , ,'));
        } finally {
            ini_set('pcre.jit', (string) $jit);
            ini_set('pcre.backtrack_limit', (string) $backtrackLimit);
        }
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'addressLine';
    }
}
