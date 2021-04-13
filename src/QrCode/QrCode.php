<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\QrCode;

use Endroid\QrCode\QrCode as BaseQrCode;
use Endroid\QrCode\QrCodeInterface;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;

final class QrCode extends BaseQrCode implements QrCodeInterface
{
    public const FILE_FORMAT_PNG = 'png';
    public const FILE_FORMAT_SVG = 'svg';

    // A file extension is supported if the underlying library supports it,
    // including the possibility to add a logo in the center of the qr code.
    private const SUPPORTED_FILE_FORMATS = [
        self::FILE_FORMAT_PNG,
        self::FILE_FORMAT_SVG
    ];

    public function writeFile(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->setWriterByExtension($extension);

        $this->forceXlinkHrefIfNecessary();

        parent::writeFile($path);
    }

    public function writeDataUri(): string
    {
        $this->forceXlinkHrefIfNecessary();

        return parent::writeDataUri();
    }

    public function setWriterByExtension(string $extension): void
    {
        if (!in_array($extension, self::SUPPORTED_FILE_FORMATS)) {
            throw new UnsupportedFileExtensionException(sprintf(
                'The qr code file cannot be created. Only these file extensions are supported: %s. You provided: %s.',
                implode(', ', self::SUPPORTED_FILE_FORMATS),
                $extension
            ));
        }

        parent::setWriterByExtension($extension);
    }

    private function forceXlinkHrefIfNecessary(): void
    {
        if ($this->getWriter()->supportsExtension(self::FILE_FORMAT_SVG)) {
            $this->setWriterOptions([
                'force_xlink_href' => true
            ]);
        }
    }
}
