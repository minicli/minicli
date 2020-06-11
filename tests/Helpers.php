<?php

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Output\OutputHandler;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\CLIColors;

function getCommandsPath()
{
    return __DIR__ . '/Assets/Command';
}

function getBasicApp()
{
    $config = [
        'app_path' => getCommandsPath()
    ];

    return new App($config);
}

function getProdApp()
{
    $config = [
        'app_path' => getCommandsPath(),
        'debug' => false
    ];

    return new App($config);
}

function getThemedApp()
{
    $config = [
        'app_path' => getCommandsPath(),
        'theme' => '\Unicorn',
    ];

    return new App($config);
}

function getCommandCall(array $parameters = null)
{
    return new CommandCall(array_merge(['minicli'], $parameters));
}

function getRegistry()
{
    $config = [
        'app_path' => getCommandsPath()
    ];

    $app = new App($config);
    $app->registerCommand("minicli-test", function () {
        return true;
    });

    /** @var CommandRegistry $registry */
    $registry = $app->command_registry;

    return $registry;
}
