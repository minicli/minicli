<?php

declare(strict_types=1);

use Minicli\Console\ArgumentsHandler;
use Minicli\Console\CommandCall;
use Minicli\Exceptions\CastException;
use Minicli\Exceptions\MissingParametersException;

it('extracts argument metadata from method signature', function (): void {
    $method = new ReflectionMethod(ArgumentFixtureCommand::class, 'run');
    $handler = new ArgumentsHandler($method->getParameters());

    $argumentsInfo = $handler->extractArgumentInfo();

    expect($argumentsInfo)->toHaveCount(5)
        ->and($argumentsInfo[0]->name)->toBe('name')
        ->and($argumentsInfo[0]->required)->toBeTrue()
        ->and($argumentsInfo[2]->name)->toBe('--shout')
        ->and($argumentsInfo[2]->required)->toBeFalse();
});

it('prepares and casts method arguments from command input', function (): void {
    $method = new ReflectionMethod(ArgumentFixtureCommand::class, 'run');
    $handler = new ArgumentsHandler($method->getParameters());
    $input = new CommandCall([
        'minicli',
        'test',
        'run',
        'name=erika',
        'tags=first,second',
        'status=open',
        '--shout',
    ]);

    $arguments = $handler->prepareArguments($input);

    expect($arguments[0])->toBe('erika')
        ->and($arguments[1])->toBe(['first', 'second'])
        ->and($arguments[2])->toBeTrue()
        ->and($arguments[3])->toBe(ArgumentFixtureStatus::Open)
        ->and($arguments[4])->toBeNull();
});

it('throws for missing required parameters', function (): void {
    $method = new ReflectionMethod(ArgumentFixtureCommand::class, 'run');
    $handler = new ArgumentsHandler($method->getParameters());
    $handler->prepareArguments(new CommandCall(['minicli', 'test', 'run']));
})->throws(MissingParametersException::class);

it('throws for invalid scalar casts', function (): void {
    $method = new ReflectionMethod(ArgumentFixtureCastCommand::class, 'run');
    $handler = new ArgumentsHandler($method->getParameters());
    $handler->prepareArguments(new CommandCall(['minicli', 'test', 'run', 'count=nope']));
})->throws(CastException::class);

enum ArgumentFixtureStatus
{
    case Open;
    case Closed;
}

final class ArgumentFixtureCommand
{
    public function run(
        string $name,
        array $tags = [],
        bool $shout = false,
        ArgumentFixtureStatus $status = ArgumentFixtureStatus::Open,
        ?int $attempts = null,
    ): void {
        $payload = [$name, $tags, $shout, $status, $attempts];
        unset($payload);
    }
}

final class ArgumentFixtureCastCommand
{
    public function run(int $count): void
    {
        $GLOBALS['argument_fixture_count'] = $count;
    }
}
