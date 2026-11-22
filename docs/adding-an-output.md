# Adding an output to this library

An output renders the payment part of a QR bill with a specific library or in a specific format.

Every output in this library has to be maintained for as long as it is part of it. Therefore new outputs are only accepted if they meet the requirements below. Outputs which do not meet them can still be maintained outside of this library, see [Maintaining your own output](#maintaining-your-own-output).

## Before you start

* Open an issue and describe which library or format you would like to support. This avoids work on an output which will not be accepted.
* Check whether an existing output can already be used with the library, e.g. with some configuration. In that case, an example or a note in the documentation might be all that is needed.

## Requirements

### 1. The library is relevant

* It is widely used.
* It is actively maintained.
* It supports the PHP versions supported by this library.

### 2. The output complies with the specification

* Dimensions, positions, fonts and font sizes follow the [Style Guide QR-bill](specs/style-guide-en-v1.1.pdf).
* The QR code is displayed at its specified size, without any distortion.
* The result has been compared visually to an existing output. The pull request describes how.

### 3. All features are supported

* All settings of `DisplayOptions`.
* All languages of `Translation`.
* All options which the existing outputs offer, as far as the library allows them.
* Limitations of the library are documented in the docblock of the output. Options which cannot be supported throw an exception instead of silently producing a different result.

### 4. The output does not interfere with the user's document

If an output draws into a document provided by the user, it shares the state of that document with the user's own code. Any setting of the document which affects how content is displayed is part of this state.

**a) The result does not depend on the state of the document.**
The payment part looks exactly the same, regardless of how the user has set up the document before adding it. Therefore the output explicitly sets every setting it relies on, instead of relying on the default values of the library.

**b) The document is left as it was found.**
Content which the user adds after the payment part looks the same as if the payment part had not been added. Therefore the output restores every setting it has changed before it returns, also if an error occurs.

Both rules are covered by tests: the payment part is added to a document with non-default settings, the result is compared to the result in a document with default settings, and the settings are checked to have their previous values afterwards.

### 5. The output is tested

* All test cases of `TraitValidQrBillsProvider` are rendered with all display options and compared to reference files.
* The reference files are stable: the same code produces the same reference files with all supported versions of the library and in all environments. If the generated files contain varying data (e.g. creation dates, ids or compression), only the relevant parts are compared.
* Rule 4 is covered by tests.
* All checks of the continuous integration pass.

### 6. The output is packaged and documented

* The library is added to `require-dev` and `suggest` in `composer.json`, but not to `require`.
* An example is added to `example/`.
* The library is listed in the `README.md`.

## Removal of outputs

An output may be deprecated and removed in an upcoming major release, if its library is no longer maintained, has been superseded or is no longer widely used, or if the output hinders further development of this library.

## Maintaining your own output

You can always write and maintain your own output outside of this library, e.g. in your own project or as a separate package. To do so, implement `OutputInterface` or extend `AbstractOutput`. The existing outputs in `src/PaymentPart/Output/` can serve as a reference.
