<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Console\CommandRegistry;
use Minicli\Exceptions\CommandNotFoundException;

it('boots app with registry service', function (): void {
    $app = getBasicApp();

    expect($app)->toBeInstanceOf(App::class)
        ->and($app->commandRegistry)->toBeInstanceOf(CommandRegistry::class);
});

it('loads attributed services from fixture app', function (): void {
    $app = getConfiguredApp();

    expect($app->test)->not->toBeNull()
        ->and($app->test->hello())->toBe('Hello World!');
});

it('runs help command when no command is provided', function (): void {
    $app = getConfiguredApp();

    ob_start();
    $result = $app->runCommand(['minicli']);
    $output = (string) ob_get_clean();

    expect($result)->toBe(0)
        ->and($output)->toContain('Available commands:')
        ->and($output)->toContain('test');
});

it('executes default subcommand when parent command is called', function (): void {
    $app = getConfiguredApp();

    $result = $app->runCommand(['minicli', 'test']);

    expect($result)->toBe(0);
})->expectOutputString('Hello world');

it('throws for unknown command', function (): void {
    getConfiguredApp()->runCommand(['minicli', 'missing-command']);
})->throws(CommandNotFoundException::class);
