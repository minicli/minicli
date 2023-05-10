<?php

use Minicli\Command\CommandNamespace;
use Minicli\Command\CommandController;

function getCommandNamespace()
{
    return new CommandNamespace("Test");
}

it('asserts that a name is set as expected')
    ->expect(fn () => getCommandNamespace()->getName())
    ->toBe("Test");

it('asserts that controllers are loaded successfully')
    ->expect(fn () => getCommandNamespace()->loadControllers(getCommandsPath()))
    ->toBeArray()
    ->not()->toBeEmpty()
    ->toContainOnlyInstancesOf(CommandController::class);

it('asserts that no controllers are returned if the namespace is empty')
    ->expect(fn () => (new CommandNamespace("Empty"))->loadControllers(getCommandsPath()))
    ->toBeArray()
    ->toBeEmpty();
