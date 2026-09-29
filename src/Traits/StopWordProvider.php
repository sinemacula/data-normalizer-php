<?php

declare(strict_types = 1);

namespace SineMacula\Foundation\Normalizers\Traits;

use SineMacula\Foundation\Normalizers\Concerns\StopWordProvider as ConcernsStopWordProvider;

/**
 * The stop word provider trait at its former location.
 *
 * Kept so classes importing the original namespace keep resolving while they
 * move to the Concerns location.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @deprecated  Use \SineMacula\Foundation\Normalizers\Concerns\StopWordProvider instead;
 *              this trait is removed in 2.0.0.
 */
trait StopWordProvider // phpcs:ignore SineMacula.Namespaces.RequireConcernsNamespace.NotInConcerns
{
    use ConcernsStopWordProvider;
}
