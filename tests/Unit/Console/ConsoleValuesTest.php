<?php

declare(strict_types=1);

use Minicli\Console\ArgumentInfo;
use Minicli\Console\CommandRegisterInfo;
use Minicli\Console\ExitCode;
use Minicli\Console\GlobalFlag;

it('stores console value objects', function (): void {
    $argument = new ArgumentInfo(name: 'name', description: 'User name', required: true);
    $register = new CommandRegisterInfo(name: 'sync', description: 'Sync records');

    expect($argument->name)->toBe('name')
        ->and($argument->required)->toBeTrue()
        ->and($register->name)->toBe('sync');
});

it('defines global flags and exit codes', function (): void {
    expect(GlobalFlag::HELP->value)->toBe('--help')
        ->and(GlobalFlag::QUIET->value)->toBe('--quiet')
        ->and(ExitCode::Success->value)->toBe(0)
        ->and(ExitCode::Failure->value)->toBe(1)
        ->and(ExitCode::Invalid->value)->toBe(2);
});
