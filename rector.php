<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(
        php83: false,
        php82: false,
        php81: true,
    )
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
    ])
    ->withSymfonyContainerPhp(__DIR__. '/var/cache/dev/App_KernelDevDebugContainer.php')
    ->withImportNames()
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
