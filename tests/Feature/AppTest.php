<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Console\CommandRegistry;
use Minicli\Exceptions\CommandNotFoundException;

it('assert App is created')
    ->expect(fn (): App => getBasicApp())
    ->toBeInstanceOf(App::class);

it('asserts App sets, gets and prints signature', function (): void {
    $app = getBasicApp();
    expect($app->me)->toBe('Pest.php');
});

it('asserts App has CommandRegistry Service')
    ->expect(fn () => getBasicApp()->commandRegistry)
    ->toBeInstanceOf(CommandRegistry::class);

it('asserts App returns null when a service is not found')
    ->expect(fn () => getBasicApp()->inexistent_service)
    ->toBeNull();

it('asserts App can handle a closure as a service', function (): void {
    $app = getBasicApp();
    $app->addService('closure', fn (): string => 'closure');

    expect($app->closure)->toBe('closure');
});

it('asserts Closure service gets passed the App instance', function (): void {
    $app = getBasicApp();
    $app->addService('closure', fn ($app) => $app);

    expect($app->closure)->toBe($app);
});

it('asserts App can load service from config file', function (): void {
    $app = getConfiguredApp();
    expect($app->test->hello())->toBe('Hello World!');
});

it('asserts App registers and executes single command', function (): void {
    $app = getBasicApp();

    $app->registerCommand('minicli-test', new Minicli\Console\CommandInfo(function (): Minicli\Console\ExitCode {
        echo 'testing minicli';

        return Minicli\Console\ExitCode::Success;
    }, 'minicli-test'));

    $app->runCommand(['minicli', 'minicli-test']);
})->expectOutputString('testing minicli');

it('asserts App executes command from namespace', function (): void {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test']);
})->expectOutputString('test default');

it('registers multiple commands', function (): void {
    // Create a new instance of the App
    $app = getBasicApp();

    // Define the commands to register
    $commands = [
        'command1' => new Minicli\Console\CommandInfo(function ($input): void {}, 'command1'),
        'command2' => new Minicli\Console\CommandInfo(function ($input): void {}, 'command2'),
    ];

    // Call the registerCommands method
    $app->registerCommands($commands);
    $commandRegistry = $app->commandRegistry;

    // Assert that each command is registered correctly
    foreach ($commands as $name => $commandInfo) {
        expect($commandRegistry->getCommand($name))->toBe($commandInfo);
    }
});
it('asserts App prints signature when no command is specified', function (): void {
    $app = getBasicApp();

    $app->runCommand(['minicli']);
})->expectOutputString("\n./minicli help\n");

it('asserts App throws exception when single command is not found', function (): void {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'minicli-test-error']);
})->expectException(CommandNotFoundException::class);

it('asserts App can check if a service is registered', function (): void {
    $app = getBasicApp();
    $app->addService('test_service', fn (): string => 'test');

    expect($app->hasService('test_service'))->toBeTrue();
    expect($app->hasService('non_existent_service'))->toBeFalse();
});

it('asserts App can list all registered services', function (): void {
    $app = getBasicApp();
    $app->addService('service1', fn (): string => 'service1');
    $app->addService('service2', fn (): string => 'service2');

    $services = $app->listServices();

    expect($services)->toBeArray();
    expect(array_key_exists('service1', $services))->toBeTrue();
    expect(array_key_exists('service2', $services))->toBeTrue();
});
