# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

## [1.0.0] - 2024-02-05

### Added

- Added PEST for testing.
- The `Router` object (`$this`) is now passed to the callback of a route for access to the router's methods.

### Changed

- Switched to PHP 8.3 as a minimum requirement.
- Switched return type of `redirect()` and `redirectTo` to `never`.
- `addRoute()` now accepts an enum `HttpMethod` instead of a string. This removes the necessity to check the validity of the input. If it's not a valid enum value, the interpreter throws a `TypeError`. Plus, it introduces enums to students.
- Checked log calls for null safety.
- `basePath` is now a property and not static anymore.
- Class constants are now typed.
- Updated all dependencies.

### Removed

- `composer.lock` is now excluded from version control for more flexibility.

## [0.3.0] - 2022-01-24

### Added

- Added two redirect methods to the router. `redirectTo()` will accept a route pattern, whereas `redirect()` will take a URL as an argument.

### Changed

- `urlFor()` now only accepts a route pattern (e.g. "/form"). No method has to be specified anymore since the method is not necessary in determining the full URL for a pattern.
- Bumped phpstan/phpstan to 1.4.

### Fixed

- Updated documentation and fixed various typos.

## [0.2.0] - 2021-12-22

### Added

- The `Router` class is now PSR-3 compatible and builds upon the `Psr\Loginterfaces`. The class uses the `LoggerAwareTrait` to instantiate a logger instance with `NullLogger`. See also [2: Add logging capabilities](https://github.com/Digital-Media/fhooe-router/issues/2).
- Logging messages that inform users about added routes, route matches, a set 404 callback and an executed 404 callback.
- Added a static `getBasePath()` method to retrieve the base path. This can be used in view templates to prepend the base path to URLs.

## [0.1.0] - 2021-12-16

### Added

- Complete `Router` class with dynamic and static functionality.
- Allowed setting routes for GET and POST protocols with patterns and invocable callbacks.
- Defined a method for adding a 404 callback handler when no route matches.
- Added a method for specifying a base path if the project is not in the server's root directory.
- Added `HandlerNotSetException` class that is thrown when no 404 callback is defined.
- Added an additional static routing method as an alternative and simpler way to approach the routing concept.
- Set up `composer.json` for the use with [Composer](https://getcomposer.org/) and [Packagist](https://packagist.org/).
- Added [phpstan](https://packagist.org/packages/phpstan/phpstan) for code analysis.
- Added extensive `README.md`.
- Added notes on Contributing.
- Added this changelog.

[Unreleased]: https://github.com/Digital-Media/fhooe-router/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/Digital-Media/fhooe-router/compare/v0.3.0...v1.0.0
[0.3.0]: https://github.com/Digital-Media/fhooe-router/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/Digital-Media/fhooe-router/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/Digital-Media/fhooe-router/releases/tag/v0.1.0
