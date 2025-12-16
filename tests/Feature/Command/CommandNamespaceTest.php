<?php

declare(strict_types=1);

use Minicli\Console\CommandController;
use Minicli\Console\CommandNamespace;

function getCommandNamespace(): CommandNamespace
{
    return new CommandNamespace('Test');
}

it('asserts that a name is set as expected')
    ->expect(fn (): string => getCommandNamespace()->getName())
    ->toBe('Test');

it('asserts that controllers are loaded successfully')
    ->expect(fn (): array => getCommandNamespace()->loadControllers(getCommandsPath()))
    ->toBeArray()
    ->not()->toBeEmpty()
    ->toContainOnlyInstancesOf(CommandController::class);

it('asserts that no controllers are returned if the namespace is empty')
    ->expect(fn (): array => new CommandNamespace('Empty')->loadControllers(getCommandsPath()))
    ->toBeArray()
    ->toBeEmpty();
