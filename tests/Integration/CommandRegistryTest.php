<?php

declare(strict_types=1);

use Minicli\Console\CommandRegistry;

it('discovers attribute-based commands from configured paths', function (): void {
    $registry = getRegistry();

    expect($registry)->toBeInstanceOf(CommandRegistry::class)
        ->and($registry->getCommand('help'))->not->toBeNull()
        ->and($registry->getCommand('test'))->not->toBeNull()
        ->and($registry->getCommand('test greet'))->not->toBeNull()
        ->and($registry->getCommand('test tags'))->not->toBeNull()
        ->and($registry->getCommand('test cast'))->not->toBeNull();
});

it('returns null when command is not registered', function (): void {
    expect(getRegistry()->getCommand('missing'))->toBeNull();
});

it('returns flat command map keyed by full command name', function (): void {
    $commandMap = getRegistry()->getCommandMap();

    expect($commandMap)->toBeArray()
        ->and(array_key_exists('help', $commandMap))->toBeTrue()
        ->and(array_key_exists('test', $commandMap))->toBeTrue()
        ->and(array_key_exists('test greet', $commandMap))->toBeTrue();
});
