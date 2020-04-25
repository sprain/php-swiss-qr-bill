<?php

namespace Sprain\SwissQrBill\QrCode;

use Endroid\QrCode\QrCode as BaseQrCode;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\WriterRegistry;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;

class QrCode extends BaseQrCode implements QrCodeInterface
{
    const FILE_FORMAT_PNG = 'png';
    const FILE_FORMAT_SVG = 'svg';

    // A file extension is supported if the underlying library supports it,
    // including the possibility to add a logo in the center of the qr code.
    const SUPPORTED_FILE_FORMATS = [
        self::FILE_FORMAT_PNG,
        self::FILE_FORMAT_SVG
    ];

    public function writeFile(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, self::SUPPORTED_FILE_FORMATS)) {
            throw new UnsupportedFileExtensionException(sprintf(
                'Your file cannot be saved. Only these file extensions are supported: %s. You provided: %s.',
                implode(', ', self::SUPPORTED_FILE_FORMATS),
                $extension
            ));
        }

        $this->setWriterByExtension($extension);
        parent::writeFile($path);
    }
}