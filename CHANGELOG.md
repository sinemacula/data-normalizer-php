# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres
to [Semantic Versioning](https://semver.org/).

## [1.0.0] - Unreleased

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

- `#[\SensitiveParameter]` on the raw-value parameters that carry PII (`Ssn`, `Clean`, and the facade
  dispatch) so unredacted values do not surface in stack traces.
- Resource-file loading rejects path-traversal sequences before touching the filesystem.

### Changed

- Pre-1.0 package; no prior releases.

### Fixed

- Pre-1.0 package; no prior fixes to enumerate.

[1.0.0]: https://github.com/sinemacula/data-normalizer-php/releases/tag/1.0.0
