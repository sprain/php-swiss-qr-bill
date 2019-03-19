# Welcome to the docs of PhpSwissQrBill

A PHP library to create Swiss QR Bill payment parts, a new standard which will replace the existing inpayment slips starting on June 30, 2020.

![Image of Swiss QR Bill example](assets/example-payment-part.png)

## Official resources
The repository contains the official specifications the library is based on:

- [Swiss Implementation Guidelines QR-bill](specs/ig-qr-bill-en-v0200.pdf)
- [Technical information about the QR-IID and QR-IBAN](specs/qr-iid_qr-iban-en.pdf)
- [Style Guide QR-bill](specs/style-guide-en.pdf)

For more official information about the new standards see

- [https://www.paymentstandards.ch/en/home/roadmap/payment-slips.html](https://www.paymentstandards.ch/en/home/roadmap/payment-slips.html)
- [https://www.six-group.com/interbank-clearing/en/home/standardization/payment-slips.html](https://www.six-group.com/interbank-clearing/en/home/standardization/payment-slips.html)

## Prerequisites
This library supports PHP 7.1 – 7.3

## Installation

```sh
composer require sprain/swiss-qr-bill
```

## Quick start example
See [example/example.php](https://github.com/sprain/php-swiss-qr-bill/tree/master/example/example.php).