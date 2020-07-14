# Swiss QR Bill

[![Build Status](https://travis-ci.org/sprain/php-swiss-qr-bill.svg?branch=master)](https://travis-ci.org/sprain/php-swiss-qr-bill)
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

In addition, a minor version will always be published if anything on the output of the qr code or the payment part changes, even if it could be considered to be only a bugfix.


## Support the project

* Do you like this project? [Consider a Github sponsorship.](https://github.com/sponsors/sprain)
* Would you like to contribute? [Have a look at the open issues.](https://github.com/sprain/php-swiss-qr-bill/issues) Be nice to each other.
* Spread the word!
