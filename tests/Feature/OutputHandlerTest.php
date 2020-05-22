<?php

use Minicli\Output\OutputHandler;
use Minicli\Output\Adapter\DefaultPrinterAdapter;

function getSimpleOutputHandler()
{
    return new OutputHandler(new DefaultPrinterAdapter());
}

it('asserts that BasicPrinter outputs expected text', function () {
    $printer = getSimpleOutputHandler();
    $printer->out("testing minicli");

})->expectOutputString("testing minicli");

it('asserts that BasicPrinter outputs raw content', function () {
    $printer = getSimpleOutputHandler();
    $printer->rawOutput("testing minicli");

})->expectOutputString("testing minicli");

it('asserts that BasicPrinter outputs newline', function () {
    $printer = getSimpleOutputHandler();
    $printer->newline();
})->expectOutputString("\n");

it('asserts that BasicPrinter displays content wrapped in newlines', function () {
    $printer = getSimpleOutputHandler();
    $printer->display("testing minicli");
})->expectOutputString("\ntesting minicli\n\n");

it('asserts that BasicPrinter displays error with expected style', function() {
    $printer = getSimpleOutputHandler();
    $printer->error("error minicli");
})->expectOutputString("\nerror minicli\n");

it('asserts that BasicPrinter displays info with expected style', function() {
    $printer = getSimpleOutputHandler();
    $printer->info("info minicli");
})->expectOutputString("\ninfo minicli\n");

it('asserts that BasicPrinter displays success with expected style', function() {
    $printer = getSimpleOutputHandler();
    $printer->success("success minicli");
})->expectOutputString("\nsuccess minicli\n");
