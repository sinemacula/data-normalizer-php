<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\Name;

/**
 * Name normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Name::class)]
final class NameTest extends TypeTestCase
{
    /** @var string The canonical normalized value for John Doe examples. */
    private const string NORMALIZED_JOHN_DOE = 'John Doe';

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
            'standard capitalization'                            => ['john doe', self::NORMALIZED_JOHN_DOE],
            'multiple spaces are normalized'                     => ['  john   doe  ', self::NORMALIZED_JOHN_DOE],
            'hyphenated name keeps segment casing'               => ['anna-marie', 'Anna-Marie'],
            'mc prefix is normalized'                            => ['mcdonald', 'McDonald'],
            'mac prefix is normalized'                           => ['macarthur', 'MacArthur'],
            'o apostrophe prefix is normalized'                  => ['o\'reilly', 'O\'Reilly'],
            'lowercase particles remain lowercase'               => ['john van damme', 'John van Damme'],
            'mixed case normalizes to title case'                => ['jOhN dOe', self::NORMALIZED_JOHN_DOE],
            'prefix plus particle stays normalized'              => ['mac van damme', 'Mac van Damme'],
            'st dot prefix is normalized'                        => ['st. john', 'St. John'],
            'st prefix without dot is normalized'                => ['st john', 'St John'],
            'multiple particles normalize correctly'             => ['mac van den berg', 'Mac van den Berg'],
            'apostrophe surname normalizes correctly'            => ['o\'neil', 'O\'Neil'],
            'mc prefix applies to hyphenated surname'            => ['mcgregor-smith', 'McGregor-Smith'],
            'empty string returns null'                          => ['', null],
            'spaces only returns null'                           => ['   ', null],
            'null input returns null'                            => [null, null],
            'la particle example normalizes'                     => ['   la pIerre', 'La Pierre'],
            'ter particle example normalizes'                    => ['teR maRk', 'Ter Mark'],
            'della particle example normalizes'                  => ['della cRoce', 'Della Croce'],
            'vanden particle example normalizes'                 => ['Vanden aPPel', 'Vanden Appel'],
            'uppercase particle normalizes'                      => ['Ludwig VAN Beethoven', 'Ludwig van Beethoven'],
            'last first comma syntax flips order'                => ['Doe, John', self::NORMALIZED_JOHN_DOE],
            'three part comma syntax stays in place'             => ['Doe, John, Jr', 'Doe, John, Jr'],
            'comma syntax with extra spaces normalizes'          => ['  Doe ,   John  ', self::NORMALIZED_JOHN_DOE],
            'comma syntax with hyphenated first name normalizes' => ['SMITH, aNNa-mArie', 'Anna-Marie Smith'],
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
        return 'name';
    }
}
