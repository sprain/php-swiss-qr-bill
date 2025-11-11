<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\QrCode;

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode as BaseQrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;

final class QrCode
{
    public const FILE_FORMAT_PNG = 'png';
    public const FILE_FORMAT_SVG = 'svg';

    public const SUPPORTED_CHARACTERS = '\x20-\x7E\xA0-\xFF\x{0100}-\x{017F}\x{0218}\x{0219}\x{021A}\x{021B}\x{20AC}';

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

    /** @var array<string, bool> $writerOptions */
    private array $writerOptions = [SvgWriter::WRITER_OPTION_FORCE_XLINK_HREF => true];

    /**
     * @param string $data
     * @param string|null $fileFormat
     * @param array<string, string> $unsupportedCharacterReplacements
     * @return self
     * @throws UnsupportedFileExtensionException
     */
    public static function create(string $data, ?string $fileFormat = null, array $unsupportedCharacterReplacements = []): self
    {
        if (null === $fileFormat) {
            $fileFormat = self::FILE_FORMAT_SVG;
        }

        return new self($data, $fileFormat, $unsupportedCharacterReplacements);
    }

    /**
     * @param string $data
     * @param string $fileFormat
     * @param array<string, string> $unsupportedCharacterReplacements
     * @throws UnsupportedFileExtensionException
     */
    private function __construct(string $data, string $fileFormat, array $unsupportedCharacterReplacements)
    {
        $data = $this->replaceUnsupportedCharacters($data, $unsupportedCharacterReplacements);
        $data = $this->cleanUnsupportedCharacters($data);

        if (class_exists(ErrorCorrectionLevelMedium::class)) {
            // Endroid 4.x
            $this->qrCode = BaseQrCode::create($data)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium())
                ->setSize(self::PX_QR_CODE)
                ->setMargin(0)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeEnlarge());
        } elseif (method_exists(BaseQrCode::class, 'create')) {
            // Endroid 5.x
            $this->qrCode = BaseQrCode::create($data)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->setSize(self::PX_QR_CODE)
                ->setMargin(0)
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Enlarge);
        } else {
            // Endroid 6.x
            $this->qrCode = new BaseQrCode(
                $data,
                new Encoding('UTF-8'),
                ErrorCorrectionLevel::Medium,
                self::PX_QR_CODE,
                0,
                RoundBlockSizeMode::Enlarge
            );
        }

        if (method_exists(Logo::class, 'create')) {
            $this->qrCodeLogo = Logo::create(self::SWISS_CROSS_LOGO_FILE)
                ->setResizeToWidth(self::PX_SWISS_CROSS);
        } else {
            $this->qrCodeLogo = new Logo(
                self::SWISS_CROSS_LOGO_FILE,
                self::PX_SWISS_CROSS
            );
        }

        $this->setWriterByExtension($fileFormat);
    }

    public function writeFile(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->setWriterByExtension($extension);
        $this->getQrCodeResult()->saveToFile($path);
    }

    public function getDataUri(string $format = self::FILE_FORMAT_SVG): string
    {
        $this->setWriterByExtension($format);
        return $this->getQrCodeResult()->getDataUri();
    }
    
    public function getAsString(string $format = self::FILE_FORMAT_SVG): string
    {
        $this->setWriterByExtension($format);
        return $this->getQrCodeResult()->getString();
    }

    public function getText(): string
    {
        return $this->qrCode->getData();
    }

    /**
     * This makes sure the file size of invoices created with TcPdfOutput is not unnecessarily inflated.
     *
     * With endroid/qr-code 5.0.8, the default behaviour was changed to create optimized SVGs with the <path> element.
     * However, for some unknown reason this inflates the filze size of invoices created with TcPdfOutput, even though
     * the file size of the qr code becomes smaller.
     * In endroid/qr-code 5.0.9, an option was added to create SVGs in the "old style" again, using <defs> elements.
     * This is what we want to use for TcPdfOutput, if available.
     *
     * @link https://github.com/sprain/php-swiss-qr-bill/issues/249
     * @link https://github.com/endroid/qr-code/commit/3dcdfab4c9122874f3915d8bf80a43b9df11852d
     */
    public function avoidCompactSvgs(): void
    {
        // The constant only exists in Endroid 5.0.9 and higher
        if (defined('Endroid\QrCode\Writer\SvgWriter::WRITER_OPTION_COMPACT')) {
            $this->writerOptions = array_merge($this->writerOptions, [
                SvgWriter::WRITER_OPTION_COMPACT => false
            ]);
        }
    }

    /**
     * @param string $data
     * @param array<string, string> $unsupportedCharacterReplacements
     * @return string
     */
    private function replaceUnsupportedCharacters(string $data, array $unsupportedCharacterReplacements): string
    {
        foreach ($unsupportedCharacterReplacements as $character => $replacement) {
            if (preg_match("/([^" . self::SUPPORTED_CHARACTERS . "])/u", $character)) {
                $data = str_replace($character, $replacement, $data);
            }
        }

        return $data;
    }

    private function cleanUnsupportedCharacters(string $data): string
    {
        $supportedCharacters = self::SUPPORTED_CHARACTERS . "\\n";

        return preg_replace("/([^$supportedCharacters])/u", '', $data);
    }

    private function setWriterByExtension(string $extension): void
    {
        if (!in_array($extension, self::SUPPORTED_FILE_FORMATS)) {
            throw new UnsupportedFileExtensionException(sprintf(
                'The qr code file cannot be created. Only these file formats are supported: %s. You provided: %s.',
                implode(', ', self::SUPPORTED_FILE_FORMATS),
                $extension
            ));
        }

        $this->qrCodeWriter = match ($extension) {
            self::FILE_FORMAT_SVG => new SvgWriter(),
            default => new PngWriter(),
        };
    }

    private function getQrCodeResult(): ResultInterface
    {
        return $this->qrCodeWriter->write(
            $this->qrCode,
            $this->qrCodeLogo,
            null,
            $this->writerOptions
        );
    }
}
