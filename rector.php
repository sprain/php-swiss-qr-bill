<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/example',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames(true);
    $rectorConfig->importShortClasses(false)

    $rectorConfig->sets([SetList::PHP_81]);

    $rectorConfig->rule(ClassPropertyAssignToConstructorPromotionRector::class);
};
