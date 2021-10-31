<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\QrCode;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\QrCode as BaseQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;

final class QrCode
{
    public const FILE_FORMAT_PNG = 'png';
    public const FILE_FORMAT_SVG = 'svg';

    // A file extension is supported if the underlying library supports it,
    // including the possibility to add a logo in the center of the qr code.
    private const SUPPORTED_FILE_FORMATS = [
        self::FILE_FORMAT_PNG,
        self::FILE_FORMAT_SVG
    ];

    private const SWISS_CROSS_LOGO_FILE = __DIR__ . '/../../assets/swiss-cross.optimized.png';
    private const PX_QR_CODE = 543;    // recommended 46x46 mm in px @ 300dpi – in pixel based outputs the final image size may be slightly different, depending on the qr code contents
    private const PX_SWISS_CROSS = 83; // recommended 7x7 mm in px @ 300dpi

    private BaseQrCode $qrCode;
    private Logo $qrCodeLogo;
    private WriterInterface $qrCodeWriter;

    public static function create(string $data, ?string $fileFormat = null): self
    {
        if (null === $fileFormat) {
            $fileFormat = self::FILE_FORMAT_SVG;
        }

        return new self($data, $fileFormat);
    }

    private function __construct(string $data, string $fileFormat)
    {
        $this->qrCode = BaseQrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->setSize(self::PX_QR_CODE)
            ->setMargin(0)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeEnlarge());

        $this->qrCodeLogo = Logo::create(self::SWISS_CROSS_LOGO_FILE)
            ->setResizeToWidth(self::PX_SWISS_CROSS);

        $this->setWriterByExtension($fileFormat);
    }

    public function writeFile(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->setWriterByExtension($extension);
        $this->getQrCodeResult()->saveToFile($path);
    }

    public function writeDataUri(): string
    {
        return $this->getQrCodeResult()->getDataUri();
    }

    public function getText(): string
    {
        return $this->qrCode->getData();
    }

    private function setWriterByExtension(string $extension): void
    {
        if (!in_array($extension, self::SUPPORTED_FILE_FORMATS)) {
            throw new UnsupportedFileExtensionException(sprintf(
                'The qr code file cannot be created. Only these file extensions are supported: %s. You provided: %s.',
                implode(', ', self::SUPPORTED_FILE_FORMATS),
                $extension
            ));
        }

        switch ($extension) {
            case self::FILE_FORMAT_SVG:
                $this->qrCodeWriter = new SvgWriter();
                break;
            case self::FILE_FORMAT_PNG:
            default:
                $this->qrCodeWriter = new PngWriter();
        }
    }

    private function getQrCodeResult(): ResultInterface
    {
        return $this->qrCodeWriter->write(
            $this->qrCode,
            $this->qrCodeLogo,
            null,
            [SvgWriter::WRITER_OPTION_FORCE_XLINK_HREF]
        );
    }
}
