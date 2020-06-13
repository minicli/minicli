<?php

use Minicli\Output\OutputHandler;
use Minicli\Output\Adapter\DefaultPrinterAdapter;

function getSimpleOutputHandler()
{
    return new OutputHandler(new DefaultPrinterAdapter());
}

it('asserts that OutputHandler outputs expected text', function () {
    $printer = getSimpleOutputHandler();
    $printer->out("testing minicli");
})->expectOutputString("testing minicli");

it('asserts that OutputHandler outputs raw content', function () {
    $printer = getSimpleOutputHandler();
    $printer->rawOutput("testing minicli");
})->expectOutputString("testing minicli");

it('asserts that OutputHandler outputs newline', function () {
    $printer = getSimpleOutputHandler();
    $printer->newline();
})->expectOutputString("\n");

it('asserts that OutputHandler displays content wrapped in newlines', function () {
    $printer = getSimpleOutputHandler();
    $printer->display("testing minicli");
})->expectOutputString("\ntesting minicli\n");

it('asserts that OutputHandler displays error', function () {
    $printer = getSimpleOutputHandler();
    $printer->error("error minicli");
})->expectOutputString("\nerror minicli\n");

it('asserts that OutputHandler displays info', function () {
    $printer = getSimpleOutputHandler();
    $printer->info("info minicli");
})->expectOutputString("\ninfo minicli\n");

it('asserts that OutputHandler displays success', function () {
    $printer = getSimpleOutputHandler();
    $printer->success("success minicli");
})->expectOutputString("\nsuccess minicli\n");

it('asserts that OutputHandler prints table', function () {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3']
    ];

    $printer = getSimpleOutputHandler();
    $printer->printTable($table);
})->expectOutputRegex('/(\s*ID\s*)/');
