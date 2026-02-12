<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Components\Component;
use Minicli\Console\CommandCall;
use Minicli\Console\CommandRegistry;
use Minicli\Container\Container;

beforeEach(function (): void {
    Container::getInstance()->flush();
    Component::resetState();
});

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
    return new App(getFixtureAppRoot());
}

function getFixtureAppRoot(): string
{
    return __DIR__ . '/Assets/Fixtures/App';
}

function getCommandCall(?array $parameters = null): CommandCall
{
    return new CommandCall(array_merge(['minicli'], $parameters));
}

function getRegistry(): CommandRegistry
{
    $app = getConfiguredApp();

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}

function getRegistryWithMultiplePaths(): CommandRegistry
{
    $app = getConfiguredApp();

    /** @var CommandRegistry $registry */
    $registry = $app->commandRegistry;

    return $registry;
}

function runInlinePhpWithInput(string $phpCode, string $stdin): string
{
    $script = "require 'vendor/autoload.php';\n{$phpCode}";

    $command = sprintf(
        'printf %%s %s | php -r %s',
        escapeshellarg($stdin),
        escapeshellarg($script),
    );

    $output = shell_exec($command);

    return is_string($output) ? $output : '';
}
