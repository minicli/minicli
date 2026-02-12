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

it('computes fallback app command path from app root', function (): void {
    $app = getBasicApp();

    /** @var Minicli\Config\AppConfig $config */
    $config = $app->config('app');

    expect($config->commandPaths)->toBe([$app->basePath() . '/app/Commands']);
});

it('loads attributed services from fixture app', function (): void {
    $app = getConfiguredApp();

    expect($app->test)->not->toBeNull()
        ->and($app->test->hello())->toBe('Hello World!')
        ->and($app->dependent)->not->toBeNull()
        ->and($app->dependent->name())->toBe('dependent-ok');
});

it('isolates container services per app instance', function (): void {
    $firstApp = getConfiguredApp();
    $firstApp->custom = 'value';

    $secondApp = getConfiguredApp();

    expect(isset($firstApp->custom))->toBeTrue()
        ->and(isset($secondApp->custom))->toBeFalse();
});

it('loads namespaced config classes', function (): void {
    $app = getConfiguredApp();
    $extra = $app->config('extra');

    expect($extra)->not->toBeNull()
        ->and($extra->value)->toBe('loaded');
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
