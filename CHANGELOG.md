# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/Digital-Media/fhooe-router/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/Digital-Media/fhooe-router/releases/tag/v0.1.0
