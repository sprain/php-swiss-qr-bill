<?php

namespace Sprain\SwissQrBill\QrCode;

use Endroid\QrCode\QrCode as BaseQrCode;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\WriterRegistry;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;

class QrCode extends BaseQrCode implements QrCodeInterface
{
    // A file extension is supported if the underlying library supports it,
    // including the possibility to add a logo in the center of the qr code.
    private const SUPPORTED_EXTENSIONS = ['png', 'svg'];

    public function writeFile(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, self::SUPPORTED_EXTENSIONS)) {
            throw new UnsupportedFileExtensionException(sprintf(
                'Your file cannot be saved. Only these file extensions are supported: %s',
                implode(', ', self::SUPPORTED_EXTENSIONS)
            ));
        }

        $this->setWriterByExtension($extension);
        parent::writeFile($path);
    }
}