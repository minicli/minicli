<?php

declare(strict_types=1);

use Minicli\Output\OutputHandler;
use Minicli\Output\Adapter\DefaultPrinterAdapter;

function getSimpleOutputHandler()
{
    return new OutputHandler(new DefaultPrinterAdapter());
}

it('asserts that OutputHandler outputs expected text', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->out("testing minicli");
})->expectOutputString("testing minicli");

it('asserts that OutputHandler outputs raw content', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->rawOutput("testing minicli");
})->expectOutputString("testing minicli");

it('asserts that OutputHandler outputs newline', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->newline();
})->expectOutputString("\n");

it('asserts that OutputHandler displays content wrapped in newlines', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->display("testing minicli");
})->expectOutputString("\ntesting minicli\n");

it('asserts that OutputHandler displays error', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->error("error minicli");
})->expectOutputString("\nerror minicli\n");

it('asserts that OutputHandler displays info', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->info("info minicli");
})->expectOutputString("\ninfo minicli\n");

it('asserts that OutputHandler displays success', function (): void {
    $printer = getSimpleOutputHandler();
    $printer->success("success minicli");
})->expectOutputString("\nsuccess minicli\n");

it('asserts that OutputHandler prints table', function (): void {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3']
    ];

    $printer = getSimpleOutputHandler();
    $printer->printTable($table);
})->expectOutputRegex('/(\s*ID\s*)/');

it('asserts a question can be asked', function (): void {
    $printer = Mockery::mock(OutputHandler::class);
    $printer->shouldReceive('ask');

    expect($printer->ask('Test'))
        ->toBeString();
});

it('throws an exception if asking a question and display method does not exist')
    ->expect(fn () => getSimpleOutputHandler()->ask(
        content: 'test',
        method: 'awesome',
    ))
    ->throws(InvalidArgumentException::class);
