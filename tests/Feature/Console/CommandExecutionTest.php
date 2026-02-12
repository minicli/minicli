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
        ->and($output)->toContain('count');
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
