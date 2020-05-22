<?php

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;

function getCommandsPath()
{
    return __DIR__ . '/Assets/Command';
}

function getBasicApp()
{
    $config = [
        'app_path' => getCommandsPath(),
        'theme' => 'unicorn',
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
        'app_path' => getCommandsPath(),
        'theme' => 'unicorn',
    ];

    $app = new App($config);
    $app->registerCommand("minicli-test", function() {
        return true;
    });

    /** @var CommandRegistry $registry */
    $registry = $app->command_registry;

    return $registry;
}