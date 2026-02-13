<?php

declare(strict_types=1);

use Minicli\Exceptions\CastException;
use Minicli\Exceptions\MissingParametersException;

it('executes subcommand with typed arguments and bool flags', function (): void {
    $result = getConfiguredApp()->runCommand([
        'minicli',
        'test',
        'greet',
        'name=erika',
        '--shout',
    ]);

    expect($result)->toBe(0);
})->expectOutputString('HELLO ERIKA');

it('casts array arguments from comma-separated params', function (): void {
    $result = getConfiguredApp()->runCommand([
        'minicli',
        'test',
        'tags',
        'tags=first,second,third',
    ]);

    expect($result)->toBe(0);
})->expectOutputString('3');

it('shows command help through global help flag', function (): void {
    ob_start();
    $result = getConfiguredApp()->runCommand(['minicli', 'test', 'cast', '--help']);
    $output = (string) ob_get_clean();

    expect($result)->toBe(0)
        ->and($output)->toContain('Require integer value')
        ->and($output)->toContain('count')
        ->and($output)->toContain('Global Flags')
        ->and($output)->toContain('--help');
});

it('runs default method when command is called without subcommand', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'test']);

    expect($result)->toBe(0);
})->expectOutputString('Hello world');

it('runs method marked with default attribute when no default method exists', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'default-attribute']);

    expect($result)->toBe(0);
})->expectOutputString('second');

it('prioritizes default method over default attribute when both exist', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'default-priority']);

    expect($result)->toBe(0);
})->expectOutputString('method-default');

it('shows error when command has subcommands and no default handlers', function (): void {
    ob_start();
    $result = getConfiguredApp()->runCommand(['minicli', 'no-default']);
    $output = (string) ob_get_clean();

    expect($result)->toBe(2)
        ->and($output)->toContain("Command 'no-default' requires a subcommand.")
        ->and($output)->toContain('Available subcommands:')
        ->and($output)->toContain('alpha')
        ->and($output)->toContain('beta');
});

it('throws for missing required parameter', function (): void {
    getConfiguredApp()->runCommand(['minicli', 'test', 'cast']);
})->throws(MissingParametersException::class);

it('throws for invalid casted value', function (): void {
    getConfiguredApp()->runCommand(['minicli', 'test', 'cast', 'count=abc']);
})->throws(CastException::class);

it('throws when command returns non ExitCode', function (): void {
    getConfiguredApp()->runCommand(['minicli', 'test', 'invalid-return']);
})->throws(RuntimeException::class, "Command 'test invalid-return' must return Minicli\\Console\\ExitCode.");

it('resets quiet mode when command fails', function (): void {
    $app = getConfiguredApp();

    try {
        $app->runCommand(['minicli', 'test', 'explode', '--quiet']);
    } catch (RuntimeException) {
        // expected for this test
    }

    ob_start();
    Minicli\Components\Text::make('after')->render();
    $output = (string) ob_get_clean();

    expect($output)->toContain('after');
});
