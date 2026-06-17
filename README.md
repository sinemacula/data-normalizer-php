# Data Normalizer for PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/sinemacula/data-normalizer-php.svg)](https://packagist.org/packages/sinemacula/data-normalizer-php)
[![Build Status](https://github.com/sinemacula/data-normalizer-php/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/sinemacula/data-normalizer-php/actions/workflows/tests.yml)
[![Quality Gates](https://github.com/sinemacula/data-normalizer-php/actions/workflows/quality-gates.yml/badge.svg?branch=master)](https://github.com/sinemacula/data-normalizer-php/actions/workflows/quality-gates.yml)
[![Maintainability](https://qlty.sh/gh/sinemacula/projects/data-normalizer-php/maintainability.svg)](https://qlty.sh/gh/sinemacula/projects/data-normalizer-php)
[![Code Coverage](https://qlty.sh/gh/sinemacula/projects/data-normalizer-php/coverage.svg)](https://qlty.sh/gh/sinemacula/projects/data-normalizer-php)
[![Total Downloads](https://img.shields.io/packagist/dt/sinemacula/data-normalizer-php.svg)](https://packagist.org/packages/sinemacula/data-normalizer-php)

Consistent, deterministic normalization for the common data types every system handles slightly differently — names,
emails, phone numbers, postal addresses, dates, currencies, and more. Each data type is encapsulated in a small,
single-purpose normalizer behind one static facade, so `Normalizer::phone($value)` returns the same canonical output
everywhere it is called.

The library is framework-agnostic — it has no dependency on Laravel or any other framework — and extensible: any
consuming application can register its own normalizers without forking the package.

## How It Works

Every normalizer implements a single contract, `NormalizerInterface`, and is reached through the `Normalizer` facade.
Calls are routed by name: `Normalizer::email($value)` resolves to the `Email` normalizer, dispatches to its
`normalize()` method, and returns the result. Resolution is memoised, so the lookup cost is paid once per name per
process.

A few rules hold across the surface:

- **Null means "could not normalize."** Every normalizer returns `null` for input it cannot produce a meaningful value
  from (non-strings, empty values, unparseable input) rather than throwing.
- **Canonical, idempotent output.** Each normalizer maps varied input to a single canonical form; re-normalizing an
  already-normalized value returns it unchanged.

## Supported Normalizers

| Normalizer           | Call                                                | Result                                                                                                                                             |
|----------------------|-----------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| `clean`              | `Normalizer::clean($value)`                         | Collapses internal whitespace and trims the ends; the building block for the other normalizers                                                     |
| `name`               | `Normalizer::name($value)`                          | Title-cases personal names; preserves `Mc` / `Mac` / `O'` prefixes, lowercases particles (`van`, `de`, `von`), and flips `Doe, John` to `John Doe` |
| `email`              | `Normalizer::email($value)`                         | Lowercases and strips spaces                                                                                                                       |
| `phone`              | `Normalizer::phone($value, ?$country)`              | Formats to E.164 via libphonenumber; defaults to the `US` region, returns `null` for invalid numbers                                               |
| `date`               | `Normalizer::date($value)`                          | Parses a set of known formats to `Y-m-d`; returns `null` for invalid calendar dates                                                                |
| `timezone`           | `Normalizer::timezone($value)`                      | Resolves to a canonical IANA timezone identifier (case-insensitive)                                                                                |
| `addressLine`        | `Normalizer::addressLine($value)`                   | Title-cases the line and strips trailing commas                                                                                                    |
| `postalCode`         | `Normalizer::postalCode($value, ?$country)`         | Validates and formats to the country's canonical form (UK/Canada spacing, US ZIP+4 hyphen); without a country, uppercases and trims                |
| `country`            | `Normalizer::country($value)`                       | Resolves a country name or code to its ISO 3166-1 alpha-2 code, with fuzzy matching for near-misses                                                |
| `administrativeArea` | `Normalizer::administrativeArea($value, ?$country)` | Resolves a state / province / region name or code to its subdivision code (defaults to the `US` country)                                           |
| `companyName`        | `Normalizer::companyName($value)`                   | Normalizes legal suffixes (`Inc`, `LLC`, `Ltd`, `GmbH`, `SARL`)                                                                                    |
| `jobTitle`           | `Normalizer::jobTitle($value)`                      | Title-cases titles while preserving acronyms (`CEO`, `IT`, `R&D`) and lowercasing stop words                                                       |
| `currency`           | `Normalizer::currency($value)`                      | Validates and uppercases to an ISO 4217 currency code                                                                                              |
| `ssn`                | `Normalizer::ssn($value)`                           | Strips to digits; preserves already-redacted values such as `***123`                                                                               |

## Installation

```bash
composer require sinemacula/data-normalizer-php
```

## Usage

```php
use SineMacula\Foundation\Normalizers\Normalizer;

Normalizer::name('SMITH, john');                // 'John Smith'
Normalizer::email(' John.Smith@Example.COM ');  // 'john.smith@example.com'
Normalizer::phone('(650) 253-0000');            // '+16502530000'
Normalizer::country('Untied States');           // 'US'  (fuzzy match)
Normalizer::postalCode('sw1a1aa', 'GB');        // 'SW1A 1AA'

Normalizer::clean('  not   a  phone  ');        // 'not a phone'
Normalizer::phone('not a phone');               // null
```

## Extending

Register your own normalizers at application bootstrap. A custom normalizer is any class implementing
`SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface`:

```php
use SineMacula\Foundation\Normalizers\Contracts\NormalizerInterface;
use SineMacula\Foundation\Normalizers\Normalizer;

class Iban implements NormalizerInterface
{
    public static function normalize(mixed $value, mixed $context = null): ?string
    {
        return is_string($value) ? strtoupper(str_replace(' ', '', $value)) : null;
    }
}

Normalizer::register('iban', Iban::class);

$iban = Normalizer::iban('de89 3704 0044 0532 0130 00'); // DE89370400440532013000
```

Registration is validated eagerly — `register()` throws an `InvalidNormalizerException` (an `InvalidArgumentException`
subclass) immediately if the class does not implement `NormalizerInterface`, so misconfiguration surfaces at bootstrap
rather than at call time. Registering the same name twice overwrites the earlier registration (last write wins).

> [!WARNING]
> Registered normalizers take precedence over the built-ins. Registering a name such as `phone` or `clean`
> intentionally replaces the built-in behaviour for every caller in the process — a deliberate feature, but one that
> can cause hard-to-trace differences in normalized output if used accidentally.

Register at bootstrap only. In long-running runtimes (Octane, Swoole, RoadRunner, queue workers) the registry is
shared process state — treat it as write-once during boot and read-only thereafter. `Normalizer::flush()` clears all
registrations and is intended for test isolation only.

For IDE completion of your custom normalizers, subclass the facade (it is intentionally non-final) purely to carry
`@method` docblocks:

```php
use SineMacula\Foundation\Normalizers\Normalizer as BaseNormalizer;

/**
 * @method static string|null iban(string $value)
 */
class Normalizer extends BaseNormalizer {}
```

## Requirements

- PHP ^8.3

## Testing

```bash
composer test                # PHPUnit suite in parallel via Paratest
composer test:coverage       # suite with Clover coverage output
composer test:mutation       # Infection mutation gate (min MSI 90)
composer test:mutation:full  # full mutation suite without thresholds
composer check               # static analysis and lint via qlty
composer format              # format via qlty
composer smells              # duplication / complexity smells via qlty
composer bench               # PHPBench suite for the hot paths
composer bench:ci            # PHPBench with CI artifact dump
composer bench:smoke         # single-rev pass to verify every subject runs
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of notable changes.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on branching, commits, code
quality, and pull requests.

## Security

If you discover a security vulnerability, please report it responsibly. See [SECURITY.md](SECURITY.md) for the
disclosure policy and contact details.

## License

Licensed under the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).
