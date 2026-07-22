# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/).

## Unreleased

## 3.0.0 - 2026-07-22

### Added

- Added a Lockout utility that shows the effective state, active environment, state source, and toggle ([#4](https://github.com/jalendport/craft-lockout/issues/4))
- Added `lockout:manage` and `lockout:bypass` permissions for controlling who can manage or bypass a lockout
- Added an `enabled` config override for forcing the state per environment ([#1](https://github.com/jalendport/craft-lockout/issues/1))
- Added customizable lock-screen headlines, 403 or 503 status responses, and custom templates ([#1](https://github.com/jalendport/craft-lockout/issues/1))
- Added a `lockout/lockout/status` console command that reports the effective state and its source

### Changed

- Replaced the dashboard widget and settings-page toggle with the Lockout utility

## 3.0.0-beta.2 - 2026-07-22

### Changed

- Changed Lockout to require `jalendport/craft-base` and PHP 8.2 or later

### Fixed

- Fixed a bug where locked-out users could not sign out
- Fixed inverted exit codes for the `lockout/lockout/enable` and `lockout/lockout/disable` console commands
- Fixed an error where programmatic plugin-settings saves emitted a PHP warning and silently disabled Lockout
- Fixed the incorrect `changelogUrl` in `composer.json`

### Security

- Restricted the enable and disable controllers to administrators making POST requests that accept JSON

## 3.0.0-beta.1 - 2025-06-25

### Added

- Added the initial Craft 5 release

## 2.0.0 - 2023-06-08

### Added

- Added the initial Craft 4 release

## 1.0.2 - 2023-06-08

### Fixed

- Fixed a deprecation warning ([#2](https://github.com/jalendport/craft-lockout/pull/2), thanks @lukasIO)

## 1.0.1 - 2020-01-03

### Fixed

- Fixed a bug where the login screen was inaccessible while Lockout was enabled

## 1.0.0 - 2019-11-27

### Added

- Added the initial release
