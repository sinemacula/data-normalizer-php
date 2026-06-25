<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\AdministrativeArea;

/**
 * Administrative area normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(AdministrativeArea::class)]
final class AdministrativeAreaTest extends TypeTestCase
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
            'us state full name resolves to code'       => ['California', 'CA'],
            'us state name with whitespace resolves'    => ['New York', 'NY'],
            'us state code remains unchanged'           => ['NY', 'NY'],
            'canada province full name resolves'        => ['Ontario', 'ON', 'CA'],
            'canada province code remains unchanged'    => ['QC', 'QC', 'CA'],
            'us state with extra spaces resolves'       => ['  Texas  ', 'TX'],
            'us state mixed case resolves'              => ['florida', 'FL'],
            'canada province with spaces resolves'      => ['  Quebec  ', 'QC', 'CA'],
            'canada province mixed case resolves'       => ['british columbia', 'BC', 'CA'],
            'invalid area returns null'                 => ['InvalidArea', null],
            'invalid country context defaults to us'    => ['California', 'CA', 'XX'],
            'empty string returns null'                 => ['', null],
            'only spaces returns null'                  => ['   ', null],
            'null input returns null'                   => [null, null],
            'null country context defaults to us'       => ['California', 'CA', null],
            'second us state example resolves'          => ['Texas', 'TX'],
            'canada british columbia resolves'          => ['British Columbia', 'BC', 'CA'],
            'canada newfoundland and labrador resolves' => ['Newfoundland and Labrador', 'NL', 'CA'],
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
        return 'administrativeArea';
    }
}
