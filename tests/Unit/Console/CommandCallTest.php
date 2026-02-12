<?php

declare(strict_types=1);

use Minicli\Console\CommandCall;

it('loads raw arguments and command names', function (): void {
    $call = new CommandCall(['minicli', 'help', 'test']);

    expect($call->getRawArgs())->toHaveCount(3)
        ->and($call->command)->toBe('help')
        ->and($call->subcommand)->toBe('test');
});

it('parses flags with and without dashes', function (): void {
    $call = new CommandCall(['minicli', 'help', 'test', '--flag']);

    expect($call->hasFlag('--flag'))->toBeTrue()
        ->and($call->hasFlag('flag'))->toBeTrue()
        ->and($call->getFlags())->toContain('--flag');
});

it('parses params including equal signs in values', function (): void {
    $call = new CommandCall(['minicli', 'help', 'test', 'name=first=john&last=doe']);

    expect($call->hasParam('name'))->toBeTrue()
        ->and($call->getParam('name'))->toBe('first=john&last=doe');
});
