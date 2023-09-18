# Time-Based One-Time Password (TOTP)
Generate and verify a Time-Based One-Time Password (TOTP) using PHP.

## Table of contents
* [Features](#features)
* [Cryptography](#cryptography)
* [Compatibility](#compatibility)
* [Support](#support)
* [License](#license)

## Features
Generate a secret - used as cryptographic key to generate the tokens
> Configure the length of the generated secret (default: 40)

Generate token - generate the numeric code that also shows up in the 2fa app
> Configure which hashing algorithm to use: SHA1, SHA265, or SHA512 (default: SHA1)

> Configure wheter to use 6 or 8 digits in the token (default: 6)

> Configure for how long a token is valid: 15, 30, or 60 seconds (default: 30)

> Option to generate historic (older) tokens (used by the verify script)

Create URI for QR code - use this ```otpauth://``` URI as the source to generate a QR code
> Configure issuer (required) and account name (optional) that show up in the 2fa app

> 🔵 Issuer: account.name ```123 456```

> ❗ QR code image generator not included

Verify user tokens - with the generated token from the system using the stored secret
> Configurable to validate 1 or more older tokens in chronological order (default: 0)

## Cryptography
Using PHP's [random_bytes()](https://www.php.net/random_bytes) which returns cryptographically secure random bytes to generate the TOTP secret.

## Compatibility
Works with TOTP compatible 2fa apps like Google Authenticator and Authy, available for Android, iOS (iPhone), Linux, macOS, and Windows. Although not all [RFC 6238](https://datatracker.ietf.org/doc/html/rfc6238) TOTP features are supported by these apps.

## Support
[Become a sponsor](https://github.com/sponsors/mrshoekstra) ❤️

## License
Code released under the [MIT license](LICENSE.md).
