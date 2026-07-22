# Lockout Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 3.0.0-beta.2 - 2026-07-22
### Security
- The enable and disable controllers now require an admin, a POST request, and a JSON `Accept` header. Previously any logged-in user could toggle Lockout, and the endpoints were reachable over GET.

### Fixed
- Locked-out users can now sign out. The control panel access check moved off plugin initialization, so authentication actions (login, logout, set-password) stay reachable while Lockout is enabled.
- The `lockout/lockout/enable` and `lockout/lockout/disable` console commands now return correct exit codes — `0` on success, non-zero on failure. They were inverted, so `set -e` deploy scripts aborted after a successful toggle.
- Programmatic plugin-settings saves no longer emit a PHP warning and silently disable Lockout.
- Corrected the `changelogUrl` in `composer.json`, which pointed at the wrong repository.

### Changed
- Lockout now requires `jalendport/craft-base` and PHP 8.2 or later.

## 3.0.0-beta.1 - 2025-06-25
### Added
- Initial Craft 5 release

## 2.0.0 - 2023-06-08
### Added
- Initial Craft 4 release

## 1.0.2 - 2023-06-08
### Fixed
- Fixed deprecation warning ([#2](https://github.com/jalendport/craft-lockout/pull/2) - thanks @lukasIO)

## 1.0.1 - 2020-01-03
### Fixed
- Bug where login screen was inaccessible when Lockout was enabled

## 1.0.0 - 2019-11-27
### Added
- Initial release
