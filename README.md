# Swiss QR Bill

[![Build Status](https://github.com/sprain/php-swiss-qr-bill/actions/workflows/tests.yml/badge.svg)](https://github.com/sprain/php-swiss-qr-bill/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sprain/php-swiss-qr-bill/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sprain/php-swiss-qr-bill/?branch=master)

A PHP library to create Swiss QR Bill payment parts (_QR-Rechnung_), a new standard which replaces the existing inpayment slips since June 30, 2020.

![Image of Swiss QR Bill example](docs/assets/example-payment-part.png)


## Getting started

```
composer require sprain/swiss-qr-bill
```

Then have a look at [example/example.php](example/example.php).


## Versioning

[Semantic versioning](https://semver.org/) is used for this library.

In addition, a minor version will always be published if any visible change in the output of the qr code or the payment part takes place, even if it could be considered to be just a bugfix.

## Getting help

Do you need help using this library?

* [Search the existing and closed issues](https://github.com/sprain/php-swiss-qr-bill/issues?q=is%3Aissue) to see if you find your answer there.
* If you still need help, you may [create an issue](https://github.com/sprain/php-swiss-qr-bill/issues) yourself to ask your question.

Please note that the maintainer of this library will not provide any support by email.
The beauty of open source software lies in the fact that everybody can benefit from each other. Therefore questions will only be answered in public.

## Support the project

* Do you like this project? [Consider a Github sponsorship.](https://github.com/sponsors/sprain)
* Would you like to contribute? [Have a look at the open issues.](https://github.com/sprain/php-swiss-qr-bill/issues) Be nice to each other.
* Spread the word!


## Official resources
The repository contains the official specifications the library is based on:

- [Swiss Implementation Guidelines QR-bill](docs/specs/ig-qr-bill-en-v2.2.pdf)
- [Technical information about the QR-IID and QR-IBAN](docs/specs/qr-iid_qr-iban-en.pdf)
- [Style Guide QR-bill](docs/specs/style-guide-en.pdf)
- [Validation Tool](https://validation.iso-payments.ch)

For more official information about the new standards see

- [https://www.paymentstandards.ch/en/home/roadmap/payment-slips.html](https://www.paymentstandards.ch/en/home/roadmap/payment-slips.html)
- [https://www.six-group.com/interbank-clearing/en/home/standardization/payment-slips.html](https://www.six-group.com/interbank-clearing/en/home/standardization/payment-slips.html)
