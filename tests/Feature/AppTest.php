<?php

use Minicli\App;
use Minicli\Command\CommandRegistry;
use Minicli\Config;
use Minicli\Output\CliPrinter;


it('asserts App is created', function () {
    $app = getBasicApp();

    assertTrue($app instanceof \Minicli\App);
});

it('asserts App sets and gets signature', function () {

    $app = getBasicApp();

    assertStringContainsString("minicli", $app->getSignature());

    $app->setSignature("Testing minicli");
    assertEquals("Testing minicli", $app->getSignature());
});

it('asserts App has Config Service', function () {

    $app = getBasicApp();

    $config = $app->config;

    assertTrue($config instanceof Config);
});

it('asserts App has CommandRegistry Service', function () {

    $app = getBasicApp();

    $registry = $app->command_registry;

    assertTrue($registry instanceof CommandRegistry);
});

it('asserts App has Printer service', function () {

    $app = getBasicApp();

    $printer = $app->printer;

    assertTrue($printer instanceof CliPrinter);
});

it('asserts App returns null when a service is not found', function () {

    $app = getBasicApp();

    $service = $app->inexistent_service;

    assertNull($service);
});

it('asserts App registers command', function () {

    $app = getBasicApp();

    $app->registerCommand('minicli-test', function() {
        return "testing minicli";
    });

    $command = $app->command_registry->getCallable('minicli-test');

    assertIsCallable($command);
});