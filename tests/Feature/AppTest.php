<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Command\CommandRegistry;
use Minicli\Config;
use Minicli\Output\OutputHandler;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Exception\CommandNotFoundException;

it('assert App is created')
    ->expect(fn() => getBasicApp())
    ->toBeInstanceOf(App::class);

it('asserts App sets, gets and prints signature', function (): void {
    $app = getBasicApp();
    $app->setOutputHandler(new OutputHandler(new DefaultPrinterAdapter()));
    expect($app->getSignature())->toContain("minicli");

    $app->setSignature("Testing minicli");
    expect($app->getSignature())->toBe("Testing minicli");

    $app->printSignature();
})->expectOutputString("\nTesting minicli\n");

it('asserts App reads configuration from config folder', function (): void {
    $app = getConfiguredApp();

    expect($app->getSignature())->toBe('Configured App')
        ->and(realpath($app->config->app_path))->toBe(realpath(__DIR__ . '/../Assets/Command'))
        ->and($app->config->theme)->toBe('unicorn')
        ->and($app->config->debug)->toBe(true);
});

it('asserts App has Config Service')
    ->expect(fn() => getBasicApp()->config)
    ->toBeInstanceOf(Config::class);

it('asserts App has CommandRegistry Service')
    ->expect(fn() => getBasicApp()->commandRegistry)
    ->toBeInstanceOf(CommandRegistry::class);

it('asserts App has Printer Service')
    ->expect(fn() => getBasicApp()->printer)
    ->toBeInstanceOf(OutputHandler::class);

it('asserts App returns null when a service is not found')
    ->expect(fn() => getBasicApp()->inexistent_service)
    ->toBeNull();

it('asserts App parses command path with @vendor tag', function (): void {
    $app = new App([
        'app_path' => '@namespace/command'
    ]);

    $registry = $app->commandRegistry;
    $paths = $registry->getCommandsPath();

    expect($paths)->toBeArray()
        ->toHaveCount(1)
        ->and($paths[0])->toEndWith("namespace/command/Command");
});

it('asserts App can handle a closure as a service', function (): void {
    $app = getBasicApp();
    $app->addService('closure', fn() => 'closure');

    expect($app->closure)->toBe('closure');
});

it('asserts Closure service gets passed the App instance', function (): void {
    $app = getBasicApp();
    $app->addService('closure', fn($app) => $app);

    expect($app->closure)->toBe($app);
});

it('asserts App can load service from config file', function (): void {
    $app = getConfiguredApp();
    expect($app->test->hello())->toBe('Hello World!');
});

it('asserts App registers and executes single command', function (): void {
    $app = getBasicApp();

    $app->registerCommand('minicli-test', function () use ($app): void {
        $app->rawOutput("testing minicli");
    });

    $command = $app->commandRegistry->getCallable('minicli-test');
    expect($command)->toBeCallable();

    $app->runCommand(['minicli', 'minicli-test']);
})->expectOutputString("testing minicli");

it('asserts App executes command from namespace', function (): void {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test']);
})->expectOutputString("test default");
it('registers multiple commands', function (): void {
    // Create a new instance of the App
    $app = getBasicApp();

    // Define the commands to register
    $commands = [
        'command1' => function ($input) use ($app): void {
            $app->success('Hello World!', false);
            $app->info('With Background!', true);
        },
        'command2' => function ($input) use ($app): void {
            $app->success('Hello World!', false);
            $app->info('With Background!', true);
        },
        // Add more commands here
    ];

    // Call the registerCommands method
    $app->registerCommands($commands);
    $commandRegistry = $app->commandRegistry;

    // Assert that each command is registered correctly
    foreach ($commands as $command => $callable) {
        expect($commandRegistry->getCallable($command))->toBe($callable);
    }
});
it('asserts App prints signature when no command is specified', function (): void {
    $app = getBasicApp();
    $app->setOutputHandler(new OutputHandler(new DefaultPrinterAdapter()));

    $app->runCommand(['minicli']);
})->expectOutputString("\n./minicli help\n");

it('asserts App throws exception when single command is not found', function (): void {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'minicli-test-error']);
})->expectException(CommandNotFoundException::class);

it('asserts App throws exception when command is not callable', function (): void {
    $app = getBasicApp();
    $app->registerCommand('minicli-test-error', "not a callable");
})->expectException(TypeError::class);

$app = new App();
$errorNotFound = $app->filterOutput("Command \"inexistent-command\" not found.", 'error');
$errorMissingParams = $app->filterOutput("Missing required parameter(s): name", 'error');

it('asserts App shows error when debug is set to false and command is not found', function (): void {
    $app = getProdApp();

    $app->runCommand(['minicli', 'inexistent-command']);
})->expectOutputString("\n{$errorNotFound}\n");

it('asserts App runs command successfully when required parameters are provided', function (): void {
    $app = getProdApp();

    $app->runCommand(['minicli', 'test', 'required', 'name=erika']);
})->expectOutputString('Hello, erika');

it('asserts App shows error when required parameters are not provided', function (): void {
    $app = getProdApp();

    $app->runCommand(['minicli', 'test', 'required']);
})->expectOutputString("\n{$errorMissingParams}\n");


it('asserts App can check if a service is registered', function (): void {
    $app = getBasicApp();
    $app->addService('test_service', fn() => 'test');

    expect($app->hasService('test_service'))->toBeTrue();
    expect($app->hasService('non_existent_service'))->toBeFalse();
});

it('asserts App can list all registered services', function (): void {
    $app = getBasicApp();
    $app->addService('service1', fn() => 'service1');
    $app->addService('service2', fn() => 'service2');

    $services = $app->listServices();

    expect($services)->toBeArray();
    expect(array_key_exists('service1', $services))->toBeTrue();
    expect(array_key_exists('service2', $services))->toBeTrue();
});
