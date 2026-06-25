<?php

declare(strict_types = 1);

namespace Tests\Unit\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use SineMacula\Foundation\Normalizers\Types\JobTitle;

/**
 * Job title normalizer test.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(JobTitle::class)]
final class JobTitleTest extends TypeTestCase
{
    /** @var string The canonical normalized software engineer title. */
    private const string SOFTWARE_ENGINEER = 'Software Engineer';

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
        return [...self::casingCases(), ...self::acronymCases()];
    }

    /**
     * Return the normalizer name.
     *
     * @return string
     */
    #[\Override]
    protected function getNormalizerName(): string
    {
        return 'jobTitle';
    }

    /**
     * Job title casing and whitespace cases.
     *
     * @return array<string, array<int, mixed>>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function casingCases(): array
    {
        return [
            'title case remains unchanged'                   => [self::SOFTWARE_ENGINEER, self::SOFTWARE_ENGINEER],
            'uppercase title is normalized'                  => ['SOFTWARE ENGINEER', self::SOFTWARE_ENGINEER],
            'lowercase title is normalized'                  => ['software engineer', self::SOFTWARE_ENGINEER],
            'mixed case title is normalized'                 => ['sOfTwArE EnGiNeEr', self::SOFTWARE_ENGINEER],
            'title with outer spaces is trimmed'             => ['  Software Engineer  ', self::SOFTWARE_ENGINEER],
            'title with extra internal spaces is normalized' => ['Software    Engineer', self::SOFTWARE_ENGINEER],
            'hyphenated title remains unchanged'             => ['Vice-President', 'Vice-President'],
            'empty string returns null'                      => ['', null],
            'spaces only returns null'                       => ['   ', null],
            'null input returns null'                        => [null, null],
        ];
    }

    /**
     * Job title acronym cases.
     *
     * @return array<string, array<int, mixed>>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private static function acronymCases(): array
    {
        return [
            'title with parenthesized acronym remains unchanged' => [
                'Chief Technical Officer (CTO)',
                'Chief Technical Officer (CTO)',
            ],
            'ceo acronym remains uppercase' => [
                'Chief Executive Officer (CEO)',
                'Chief Executive Officer (CEO)',
            ],
            'it acronym remains uppercase'           => ['Vice President of IT', 'Vice President of IT'],
            'r and d acronym normalizes case'        => ['Director of R&d', 'Director of R&D'],
            'qa acronym normalizes case'             => ['Lead qA Engineer', 'Lead QA Engineer'],
            'ui ux acronym remains uppercase'        => ['Senior UI/UX Designer', 'Senior UI/UX Designer'],
            'devops acronym normalizes case'         => ['Devops Engineer', 'DevOps Engineer'],
            'ar vr acronym remains uppercase'        => ['AR/VR Developer', 'AR/VR Developer'],
            'it acronym in sentence normalizes case' => [
                'Manager of it and Operations',
                'Manager of IT and Operations',
            ],
            'ux ui acronym order normalizes case' => ['ux/ui Designer', 'UX/UI Designer'],
        ];
    }
}
