<?php

use Minicli\Command\CommandNamespace;
use Minicli\Exception\CommandNotFoundException;

it('asserts Registry autoloads command namespaces', function () {
    $registry = getRegistry();
    $namespace = $registry->getNamespace("test");

    $this->assertNotNull($namespace);
    $this->assertTrue($namespace instanceof CommandNamespace);
});

it('asserts Registry autoloads command namespaces in multiple source paths', function () {
    $registry = getRegistryWithMultiplePaths();
    $namespace1 = $registry->getNamespace("test");
    $namespace2 = $registry->getNamespace("vendor");

    $this->assertNotNull($namespace1);
    $this->assertNotNull($namespace2);
    $this->assertTrue($namespace1 instanceof CommandNamespace);
    $this->assertTrue($namespace2 instanceof CommandNamespace);
});

it('asserts Registry returns null when a namespace is not found', function () {
    $registry = getRegistry();
    $namespace = $registry->getNamespace("dasdsad");

    $this->assertNull($namespace);
});

it('asserts Registry returns correct controller from namespace when no subcommand is passed', function () {
    $registry = getRegistry();
    $controller = $registry->getCallableController("test");

    $this->assertTrue($controller instanceof \Assets\Command\Test\DefaultController);
});

it('asserts Registry returns correct controller from namespace when a subcommand is passed', function () {
    $registry = getRegistry();
    $controller = $registry->getCallableController("test", "help");

    $this->assertTrue($controller instanceof \Assets\Command\Test\HelpController);
});

it('asserts Registry returns null when a namespace controller is not found', function () {
    $registry = getRegistry();
    $controller = $registry->getCallableController("dasdsad");

    $this->assertNull($controller);
});

it('asserts Registry returns correct callable', function () {
    $registry = getRegistry();
    $callable = $registry->getCallable("minicli-test");

    $this->assertTrue(is_callable($callable));
});

it('asserts Registry throws CommandNotFoundException when a command is not found', function () {
    $registry = getRegistry();
    $callable = $registry->getCallable("dasdakjsdasd");
})->throws(CommandNotFoundException::class);

it('assets Registry returns full command list', function () {
    $registry = getRegistry();

    $commandList = $registry->getCommandMap();
    $this->assertCount(2, $commandList);
    $this->assertCount(4, $commandList['test']);
});

it('assets Registry returns full command list when with multiple command sources', function () {
    $registry = getRegistryWithMultiplePaths();

    $commandList = $registry->getCommandMap();
    $this->assertCount(2, $commandList);
    $this->assertCount(4, $commandList['test']);
    $this->assertCount(1, $commandList['vendor']);
});
