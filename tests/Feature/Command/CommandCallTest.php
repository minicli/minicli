<?php

declare(strict_types=1);

use Minicli\Command\CommandCall;

it('asserts input arguments are loaded and properties are set')
    ->expect(fn () => new CommandCall(["minicli", "help", "test"]))
    ->getRawArgs()->toHaveCount(3)
    ->command->toBe("help")
    ->subcommand->toBe("test");

it('asserts flags are correctly set')
    ->expect(fn () => new CommandCall(["minicli", "help", "test", "--flag"]))
    ->hasFlag("--flag")->toBeTrue()
    ->getFlags()->toContain("--flag");

it('asserts flags can be obtained without "--"')
    ->expect(fn () => new CommandCall(["minicli", "help", "test", "--flag"]))
    ->hasFlag("flag")->toBeTrue();

it('asserts params are correctly set')
    ->expect(fn () => new CommandCall(["minicli", "help", "test", "name=test"]))
    ->hasParam("name")->toBeTrue()
    ->getParam("name")->toBe("test");

it('asserts params are correctly set if value contains "="')
    ->expect(fn () => new CommandCall(["minicli", "help", "test", "name=first=john&last=doe"]))
    ->hasParam("name")->toBeTrue()
    ->getParam("name")->toBe("first=john&last=doe");
