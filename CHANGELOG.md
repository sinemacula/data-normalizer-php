# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres
to [Semantic Versioning](https://semver.org/).

## [1.0.1] - 2026-06-07

### Fixed

- Declared the optional `$country` parameter on the `postalCode` facade `@method` annotation so static
  analysis and IDEs accept the documented two-argument call (`Normalizer::postalCode($value, $country)`),
  matching the `phone` and `administrativeArea` declarations.

## [1.0.0] - 2026-06-06

Initial public release of `sinemacula/data-normalizer-php` — a framework-agnostic library for consistent,
deterministic normalization of common data types.

### Added

- `Normalizer` static facade routing calls to single-purpose normalizers through the `NormalizerInterface`
  contract, with memoised name resolution.
- Fourteen built-in normalizers: `clean`, `name`, `email`, `phone`, `date`, `timezone`, `addressLine`,
  `postalCode`, `country`, `administrativeArea`, `companyName`, `jobTitle`, `currency`, and `ssn`.
  Country-aware normalizers (`phone`, `postalCode`, `administrativeArea`) accept a country/region context;
  `postalCode` validates and formats per country via `brick/postcode`.
- Registry-backed extensibility: `Normalizer::register()` lets consuming applications add or override
  normalizers without forking; registered normalizers take precedence over the built-ins.
- Eager registration validation via `InvalidNormalizerException`, and `Normalizer::flush()` for test isolation.
- Package exception hierarchy under a shared `NormalizerExceptionInterface` marker
  (`InvalidNormalizerException`, `InvalidResourceFileException`, `ResourceFileNotFoundException`).
- PHPBench benchmark suite covering every normalizer hot path plus registry dispatch.
- Infection mutation-testing gate at 90% MSI for the scoped config; a full-repo config is available for
  diagnostic runs.

### Security

- `#[\SensitiveParameter]` on every raw-value parameter that can carry PII — the facade dispatch plus each
  PII normalizer (`Name`, `Email`, `Phone`, `AddressLine`, `PostalCode`, `AdministrativeArea`, `Ssn`, and
  `Clean`) — so unredacted values do not surface in stack traces.
- Resource-file loading validates filenames against a strict allowlist before touching the filesystem.

### Changed

- Pre-1.0 package; no prior releases.

### Fixed

- Pre-1.0 package; no prior fixes to enumerate.

[1.0.1]: https://github.com/sinemacula/data-normalizer-php/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/sinemacula/data-normalizer-php/releases/tag/v1.0.0
