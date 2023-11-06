# Changelog

## [0.5.2](https://github.com/AlexisJehan/Amity/releases/tag/v0.5.2) (2023-11-06)

### Bug fixes
- Fix the _CLI_ mode detection with _PHP 5.3_

## [0.5.1](https://github.com/AlexisJehan/Amity/releases/tag/v0.5.1) (2023-04-24)

### Bug fixes
- Fix the regular expression of the `Template->indent()` method

## [0.5.0](https://github.com/AlexisJehan/Amity/releases/tag/v0.5.0) (2023-04-19)

### Breaking changes
- Rename the `Template::is()` method to `Template::exists()`
- Remove `Fragment->escape()` and `Fragment->unescape()` methods
- Remove the `Template->bindHtml()` method

### New features
- Add the `Template->indent()` method

### Improvements
- Improve compatibility in _CLI_ mode

## [0.4.1](https://github.com/AlexisJehan/Amity/releases/tag/v0.4.1) (2023-01-27)

### Improvements
- Improve compatibility with _PHP 8.1_

## [0.4.0](https://github.com/AlexisJehan/Amity/releases/tag/v0.4.0) (2023-01-25)

### Improvements
- Improve compatibility with _PHP 8.0_ and _PHP 8.2_

## [0.3.3](https://github.com/AlexisJehan/Amity/releases/tag/v0.3.3) (2022-08-29)

### Bug fixes
- Fix the `url()` function when the language service is enabled

## [0.3.2](https://github.com/AlexisJehan/Amity/releases/tag/v0.3.2) (2021-10-28)

### Bug fixes
- Fix escaping values in the `Template` class

## [0.3.1](https://github.com/AlexisJehan/Amity/releases/tag/v0.3.1) (2021-10-25)

### Bug fixes
- Fix binding `NULL` values in the `Template` class

## [0.3.0](https://github.com/AlexisJehan/Amity/releases/tag/v0.3.0) (2021-08-02)

### Breaking changes
- Remove the `tools` package
- Remove `AgentDetector` and `TorDetector` classes

### New features
- Add a force HTTPS option to the configuration

### Notes
- Move `LogParser` and `WebRequest` classes to the `utils` package
- Rename the `style.css` file to `stylesheet.css`

## [0.2.5](https://github.com/AlexisJehan/Amity/releases/tag/v0.2.5) (2020-11-25)

### Improvements
- Improve `path()` and `url()` functions

## [0.2.4](https://github.com/AlexisJehan/Amity/releases/tag/v0.2.4) (2020-09-28)

### New features
- Add custom database options to the configuration

### Notes
- Change the default database encoding from `utf8` to `utf8mb4`

## [0.2.3](https://github.com/AlexisJehan/Amity/releases/tag/v0.2.3) (2020-06-12)

### Bug fixes
- Fix HTTP headers parsing in the `WebRequest` class

### Improvements
- Improve compatibility with _PHP 7.4_

## [0.2.2](https://github.com/AlexisJehan/Amity/releases/tag/v0.2.2) (2018-04-04)

### Bug fixes
- Fix a missing array initialisation in the `DebugService` class

### Improvements
- Improve the `path()` function

### Notes
- Migrate to _GitHub_

## 0.2.1 (2015-11-11)
Private release

## 0.2.0 (2015-09-10)
Private release

## 0.1.6 (2015-08-07)
Private release

## 0.1.5 (2015-07-18)
Private release

## 0.1.4 (2015-06-23)
Private release

## 0.1.0 (2015-05-13)
Private release

## 0.0.2 (2015-04-22)
Private release

## 0.0.1 (2015-03-19)
Private release