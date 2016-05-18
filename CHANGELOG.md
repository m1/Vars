# Changelog

All Notable changes to `Vars` will be documented in this file

## 1.1.2 - 2016-05-18

### Altered
- Support symfony/filesystem 3.x

## 1.1.1 - 2016-02-17

### Fixes
- `_globals` are now cached

## 1.1.0 - 2016-02-05

### Added
- Support for `_globals`
- Support to merge globals into `$app` for Silex

## 1.0.0 - 2016-01-19

### Added
- Added support for in-file variables

### Altered
- Syntax for all of the different variables for use in `vars`

### Removed
- Support for `$app['vars.key']` retrieval of keys

## 0.6.0 - 2016-01-08

### Added
- Added dot notation access to `$app` for the Silex service provider

## 0.5.0 - 2016-01-07

### Altered
- Bumped package versions
- Moved over `EnvLoader` to use new api of [`m1/env`](https://github.com/m1/env)
- Update README to remove typos

## 0.4.0 - 2015-12-21

### Altered
- Made it so a folder is created inside the cache path for the cache file to be stored

## 0.3.2 - 2015-12-21

### Fixed
- Fixed composer.json

## 0.3.1 - 2015-12-21

### Altered
- Changed it so you need to use if else import flags in quotes

## 0.3.0 - 2015-12-19

### Added
- Add recursive dir toggle flag
- Add suppression flag
- Add if else flag
- Add path trait

### Altered
- Moved path logic to path trait

### Fixed
- README format

## 0.2.0 - 2015-12-16

### Added
- Add recursive dir toggle
- Add support for .env file parsing via [`m1/env`](https://github.com/m1/env)
- Add support for environment variable replacements
- `toEnv()` function
- `toDots()` function
- API info to README

### Altered
- Moved loader and extension logic to `LoaderProvider`

### Fixed
- Fixed few README.md links
- Clean up `FileResource`

## 0.1.0 - 2015-12-07

### Added
- Initial Release
