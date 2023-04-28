<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;

function getCommandsPath(): string
{
    return __DIR__ . '/Assets/Command';
}

function getBasicApp(): App
{
    return new App([
        'app_path' => getCommandsPath()
    ]);
}

function getProdApp(): App
{
    return new App([
        'app_path' => getCommandsPath(),
        'debug' => false
    ]);
}

function getThemedApp(): App
{
    return new App([
        'app_path' => getCommandsPath(),
        'theme' => '\Unicorn',
    ]);
}

function getCommandCall(array $parameters = null): CommandCall
{
    return new CommandCall(array_merge(['minicli'], $parameters));
}

function getRegistry(): CommandRegistry
{
    $app = new App([
        'app_path' => getCommandsPath()
    ]);
    $app->registerCommand("minicli-test", function () {
        return true;
    });

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}

function getRegistryWithMultiplePaths(): CommandRegistry
{
    $app = new App([
        'app_path' => [
            getCommandsPath(),
            __DIR__ . '/Assets/VendorCommand'
        ]
    ]);

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}
