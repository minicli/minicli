<?php

use Minicli\Command\CommandRegistry;
use Minicli\Config;
use Minicli\Output\OutputHandler;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Exception\CommandNotFoundException;

it('asserts App is created', function () {
    $app = getBasicApp();

    $this->assertTrue($app instanceof \Minicli\App);
});

it('asserts App sets, gets and prints signature', function () {
    $app = getBasicApp();
    $app->setOutputHandler(new OutputHandler(new DefaultPrinterAdapter()));

    $this->assertStringContainsString("minicli", $app->getSignature());

    $app->setSignature("Testing minicli");
    $this->assertEquals("Testing minicli", $app->getSignature());

    $app->printSignature();
})->expectOutputString("\nTesting minicli\n");

it('asserts App has Config Service', function () {
    $app = getBasicApp();

    $config = $app->config;

    $this->assertTrue($config instanceof Config);
});

it('asserts App has CommandRegistry Service', function () {
    $app = getBasicApp();

    $registry = $app->commandRegistry;

    $this->assertTrue($registry instanceof CommandRegistry);
});

it('asserts App parses command path with @vendor tag', function () {
    $app = new \Minicli\App([
        'app_path' => '@namespace/command'
    ]);

    $registry = $app->commandRegistry;
    $paths = $registry->getCommandsPath();
    $this->assertIsArray($paths);
    $this->assertCount(1, $paths);
    var_dump($paths);
    $this->assertStringEndsWith("namespace/command/Command", $paths[0]);
});

it('asserts App has Printer service', function () {
    $app = getBasicApp();

    $printer = $app->printer;

    $this->assertTrue($printer instanceof OutputHandler);
});

it('asserts App returns null when a service is not found', function () {
    $app = getBasicApp();

    $service = $app->inexistent_service;

    $this->assertNull($service);
});

it('asserts App registers and executes single command', function () {
    $app = getBasicApp();

    $app->registerCommand('minicli-test', function () use ($app) {
        $app->getPrinter()->rawOutput("testing minicli");
    });

    $command = $app->commandRegistry->getCallable('minicli-test');
    $this->assertIsCallable($command);

    $app->runCommand(['minicli', 'minicli-test']);
})->expectOutputString("testing minicli");

it('asserts App executes command from namespace', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test']);
})->expectOutputString("test default");

it('asserts App prints signature when no command is specified', function () {
    $app = getBasicApp();
    $app->setOutputHandler(new OutputHandler(new DefaultPrinterAdapter()));

    $app->runCommand(['minicli']);
})->expectOutputString("\n./minicli help\n");

it('asserts App throws exception when single command is not found', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'minicli-test-error']);
})->expectException(CommandNotFoundException::class);

it('asserts App throws exception when command is not callable', function () {
    $app = getBasicApp();
    $app->registerCommand('minicli-test-error', "not a callable");
})->expectException(\TypeError::class);

$app = new \Minicli\App();
$errorNotFound = $app->getPrinter()->filterOutput("Command \"inexistent-command\" not found.", 'error');

it('asserts App shows error when debug is set to false and command is not found', function () {
    $app = getProdApp();

    $app->runCommand(['minicli', 'inexistent-command']);
})->expectOutputString("\n" . $errorNotFound . "\n");
