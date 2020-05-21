<?php

use Minicli\App;
use Minicli\Command\CommandRegistry;
use Minicli\Command\CommandNamespace;
use Minicli\Exception\CommandNotFoundException;

$config = [
    'app_path' => __DIR__ . '/../Assets/Command',
    'theme' => 'unicorn',
];

$app = new App($config);
$app->registerCommand("minicli-test", function() {
    return true;
});

/** @var CommandRegistry $registry */
$registry = $app->command_registry;

it('asserts Registry autoloads command namespaces', function () use($registry) {
    $namespace = $registry->getNamespace("test");

    assertNotNull($namespace);
    assertTrue($namespace instanceof CommandNamespace);
});

it('asserts Registry returns null when a namespace is not found', function () use($registry) {
    $namespace = $registry->getNamespace("dasdsad");

    assertNull($namespace);
});

it('asserts Registry returns correct controller from namespace when no subcommand is passed', function () use($registry) {
    $controller = $registry->getCallableController("test");

    assertTrue($controller instanceof \Assets\Command\Test\DefaultController);
});

it('asserts Registry returns correct controller from namespace when a subcommand is passed', function () use($registry) {
    $controller = $registry->getCallableController("test", "help");

    assertTrue($controller instanceof \Assets\Command\Test\HelpController);
});

it('asserts Registry returns null when a namespace controller is not found', function () use($registry) {
    $controller = $registry->getCallableController("dasdsad");

    assertNull($controller);
});

it('asserts Registry returns correct callable', function () use($registry) {
    $callable = $registry->getCallable("minicli-test");

    assertTrue(is_callable($callable));
});

it('asserts Registry throws CommandNotFoundException when a command is not found', function () use($registry) {

    $callable = $registry->getCallable("dasdakjsdasd");

})->throws(CommandNotFoundException::class);