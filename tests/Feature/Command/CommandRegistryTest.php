<?php

declare(strict_types=1);

use Minicli\Command\CommandNamespace;
use Minicli\Exception\CommandNotFoundException;

it('asserts Registry autoloads command namespaces')
    ->expect(fn () => getRegistry()->getNamespace("test"))
    ->not()->toBeNull()
    ->toBeInstanceOf(CommandNamespace::class);


it('asserts Registry autoloads command namespaces in multiple source paths')
    ->expect(fn () => getRegistryWithMultiplePaths())
    ->getNamespace("test")
    ->not()->toBeNull()
    ->toBeInstanceOf(CommandNamespace::class)
    ->getNamespace("vendor")
    ->not()->toBeNull()
    ->toBeInstanceOf(CommandNamespace::class);

it('asserts Registry returns null when a namespace is not found')
    ->expect(fn () => getRegistry()->getNamespace("dasdsad"))
    ->toBeNull();

it('asserts Registry returns correct controller from namespace when no subcommand is passed')
    ->expect(fn () => getRegistry()->getCallableController("test"))
    ->toBeInstanceOf(\Assets\Command\Test\DefaultController::class);

it('asserts Registry returns correct controller from namespace when a subcommand is passed')
    ->expect(fn () => getRegistry()->getCallableController("test", "help"))
    ->toBeInstanceOf(\Assets\Command\Test\HelpController::class);

it('asserts Registry returns null when a namespace controller is not found')
    ->expect(fn () => getRegistry()->getCallableController("dasdsad"))
    ->toBeNull();

it('asserts Registry returns correct callable')
    ->expect(fn () => getRegistry()->getCallable("minicli-test"))
    ->toBeCallable();

it('asserts Registry throws CommandNotFoundException when a command is not found')
    ->expect(fn () => getRegistry()->getCallable("dasdakjsdasd"))
    ->throws(CommandNotFoundException::class);

it('assets Registry returns full command list', function (): void {
    $registry = getRegistry();
    $commandList = $registry->getCommandMap();

    expect($commandList)->toBeArray()
        ->toHaveCount(2)
        ->and($commandList['test'])->toBeArray()->toHaveCount(4);
});

it('assets Registry returns full command list when with multiple command sources', function (): void {
    $registry = getRegistryWithMultiplePaths();
    $commandList = $registry->getCommandMap();

    expect($commandList)->toBeArray()
        ->toHaveCount(2)
        ->and($commandList['test'])->toBeArray()->toHaveCount(4)
        ->and($commandList['vendor'])->toBeArray()->toHaveCount(1);
});
