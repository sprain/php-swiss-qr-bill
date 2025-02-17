<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/example',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets() //checks composer.json for supported php versions
    //->withAttributesSets(all: true)
    ;