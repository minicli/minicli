<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Console\CommandCall;
use Minicli\Console\CommandRegistry;

function getCommandsPath(): string
{
    return __DIR__ . '/Assets/Command';
}

function getBasicApp(): App
{
    return new App();
}

function getProdApp(): App
{
    return new App();
}

function getThemedApp(): App
{
    return new App();
}

function getConfiguredApp(): App
{
    return new App(__DIR__ . '/Assets');
}

function getCommandCall(?array $parameters = null): CommandCall
{
    return new CommandCall(array_merge(['minicli'], $parameters));
}

function getRegistry(): CommandRegistry
{
    $app = new App();
    $app->registerCommand('minicli-test', new Minicli\Console\CommandInfo(fn (): true => true, 'minicli-test'));

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}

function getRegistryWithMultiplePaths(): CommandRegistry
{
    $app = new App();

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}
