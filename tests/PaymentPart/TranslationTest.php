<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart;

use PHPUnit\Framework\Attributes\DataProvider;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;

final class TranslationTest extends TestCase
{
    use ArraySubsetAsserts;

    #[DataProvider('allTranslationsProvider')]
    public function testAllByLanguage(string $locale, array $subset): void
    {
        $this->assertArraySubset($subset, Translation::getAllByLanguage($locale));
    }

    public static function allTranslationsProvider(): array
    {
        return [
            ['de', ['paymentPart' => 'Zahlteil']],
            ['fr', ['paymentPart' => 'Section paiement']],
            ['it', ['paymentPart' => 'Sezione pagamento']],
            ['en', ['paymentPart' => 'Payment part']]
        ];
    }

    #[DataProvider('singleTranslationProvider')]
    public function testGet(string $locale, string $key, string $translation)
    {
        $this->assertSame($translation, Translation::get($key, $locale));
    }

    public static function singleTranslationProvider(): array
    {
        return [
            ['de', 'paymentPart', 'Zahlteil'],
            ['fr', 'paymentPart', 'Section paiement'],
            ['it', 'paymentPart', 'Sezione pagamento'],
            ['en', 'paymentPart', 'Payment part']
        ];
    }
}
