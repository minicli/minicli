<?php

use Minicli\App;
use Minicli\PrebuiltCommands\HelpCommand;

test('help command is automatically run when minicli is called without arguments', function () {
    $app = new App();

    // Create output buffer to capture printed content
    ob_start();

    $argv = ['minicli'];
    $app->runCommand($argv);
    $output = ob_get_clean();

    $expected = "Lists the available commands.";

    expect($output)->toContain($expected);
});

test('help command outputs correct CLI format', function () {
    $helpCommand = new HelpCommand(new App());

    $commands = [
        ['app help', null, 'Lists the available commands.'],
        ['app help2', null, null],
        ['app version', null, "Shows the current app's version"],
        ['app new', null, null],
        ['app deploy', '--force', "Force deploy the application"],
        ['app rollback', '--target <version>', null],
    ];

    // Create output buffer to capture printed content
    ob_start();

    // Call protected method using Reflection
    $method = new ReflectionMethod(HelpCommand::class, 'formatCommands');
    $method->setAccessible(true);
    $method->invoke($helpCommand, $commands);

    $output = ob_get_clean();

    // Define expected output with exact spacing
    $expected = <<<EOT
    app help                        — Lists the available commands.
    app help2
    app version                     — Shows the current app's version
    app new
    app deploy --force              — Force deploy the application
    app rollback --target <version>
    EOT;

    // Assert exact match including whitespace and newlines
    expect($output)->toBe($expected . "\n");
});

test('can add extra code to the prebuilt help command', function () {
    $app = new App();
    $app->prebuilt->help->registerCallback(function (string $name = 'World') {
        echo "Hello, $name!!!\n";
    }, ['name' => 'r/PHP']);

    // Create output buffer to capture printed content
    ob_start();

    $argv = ['minicli'];
    $app->runCommand($argv);
    $output = ob_get_clean();

    $expected = "Lists the available commands.";

    expect($output)->toContain($expected);
    expect($output)->toContain('Hello, r/PHP!!!');
});
