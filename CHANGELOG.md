# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [1.14] (August 2023)
### Added
- add LICENSE and NOTICE

## [1.13] (July 2022)
### Added
- Company info added to the customer object
- Added check and notification if frontend URLs have changed due to JTL/plugin updates and how to correct them
- Add VAT amount to shopping cart object

### Changed
- Unzer SDK version updated to 1.1.4.2

### Fixed
- Fixed problem with instalments sending wrong/temporary order numbers to Unzer
- Fixed unhandled error when retrieving refunds in backend

## [1.12] (March 2022)
### Added
- add minimum customer info (name and email) to all payments

### Changed
- use short id as transaction id in payment history (WaWi)

### Fixed
- add error handling to avoid issues in the frontend when API is not callable *(ie missing keys)*
- fix issue with -0.0 beeing interpreted as negative in the unzer api
- potential fix for mismatch of order ids between the unzer insight portal and the shop

## [1.11] (November 2021)
### Added
- JTL WaWi 1.6 Compatability

### Fixed
- typo in SQL Query
- diplay error for cancellations with the same ID but different charges
- problem in validation resulting in not being able to use vouchers/coupons in the last checkout step

## [1.10] (July 2021)
### Added
- Initial Release

[1.14]: https://github.com/unzerdev/jtl4/compare/1.13...1.14
[1.13]: https://github.com/unzerdev/jtl4/compare/1.12...1.13
[1.12]: https://github.com/unzerdev/jtl4/compare/1.11...1.12
[1.11]: https://github.com/unzerdev/jtl4/compare/1.10...1.11
[1.10]: https://github.com/unzerdev/jtl4/releases/tag/1.10