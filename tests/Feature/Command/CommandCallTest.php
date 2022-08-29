<?php

use Minicli\Command\CommandCall;

it('asserts input arguments are loaded and properties are set', function () {
    $call = new CommandCall(["minicli", "help", "test"]);

    $this->assertCount(3, $call->getRawArgs());
    $this->assertEquals("help", $call->command);
    $this->assertEquals("test", $call->subcommand);
});

it('asserts flags are correctly set', function () {
    $call = new CommandCall(["minicli", "help", "test", "--flag"]);

    $this->assertTrue($call->hasFlag('--flag'));
    $this->assertContains("--flag", $call->getFlags());
});

it('asserts flags can be obtained without "--"', function () {
    $call = new CommandCall(["minicli", "help", "test", "--flag"]);

    $this->assertTrue($call->hasFlag('flag'));
});

it('asserts params are correctly set', function () {
    $call = new CommandCall(["minicli", "help", "test", "name=test"]);

    $this->assertTrue($call->hasParam('name'));
    $this->assertEquals('test', $call->getParam('name'));
});
