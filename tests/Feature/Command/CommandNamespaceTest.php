<?php

use Minicli\Command\CommandNamespace;
use Minicli\Command\CommandController;

function getCommandNamespace()
{
    return new CommandNamespace("Test");
}

it('asserts that a name is set as expected', function () {
    $namespace = getCommandNamespace();

    $this->assertEquals("Test", $namespace->getName());
});

it('asserts that controllers are loaded successfully', function () {
    $namespace = getCommandNamespace();
    $controllers = $namespace->loadControllers(getCommandsPath());

    $this->assertIsArray($controllers);
    $this->assertNotEmpty($controllers);
    $this->assertContainsOnly(CommandController::class, $controllers);
});

it('asserts that no controllers are returned if the namespace is empty', function () {
    $namespace = new CommandNamespace("Empty");
    $controllers = $namespace->loadControllers(getCommandsPath());

    $this->assertCount(0, $controllers);
});
