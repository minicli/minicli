<?php

use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\Theme\UnicornTheme;
use Minicli\Output\CLIColors;
use Minicli\Output\OutputHandler;
use Minicli\Output\Filter\ColorOutputFilter;

/** Color Output Helpers */

function getColorOutputHandler()
{
    $handler = new OutputHandler();
    $handler->registerFilter(new ColorOutputFilter());

    return $handler;
}

function getDefaultOutput($text)
{
    return sprintf("\e[%sm%s\e[0m", CLIColors::$FG_WHITE, $text);
}

function getAltOutput($text)
{
    return sprintf("\e[%s;%sm%s\e[0m", CLIColors::$FG_BLACK, CLIColors::$BG_WHITE, $text);
}

function getErrorOutput($text)
{
    return sprintf("\e[%sm%s\e[0m", CLIColors::$FG_RED, $text);
}

function getInfoOutput($text)
{
    return sprintf("\e[%sm%s\e[0m", CLIColors::$FG_CYAN, $text);
}

function getSuccessOutput($text)
{
    return sprintf("\e[%sm%s\e[0m", CLIColors::$FG_GREEN, $text);
}

function getThemedOutput($text)
{
    return sprintf("\e[%sm%s\e[0m", CLIColors::$FG_MAGENTA, $text);
}

/** TESTS */

it('asserts that OutputHandler outputs correct style', function () {
    $printer = getColorOutputHandler();
    $printer->out("testing minicli", "alt");
})->expectOutputString(getAltOutput("testing minicli"));

it('asserts that OutputHandler outputs newline', function () {
    $printer = getColorOutputHandler();
    $printer->newline();
})->expectOutputString("\n");

it('asserts that OutputHandler displays content wrapped in newlines', function () {
    $printer = getColorOutputHandler();
    $printer->display("testing minicli");
})->expectOutputString("\n" . getDefaultOutput("testing minicli") . "\n");

it('asserts that OutputHandler displays error with expected style', function () {
    $printer = getColorOutputHandler();
    $printer->error("error minicli");
})->expectOutputString("\n" . getErrorOutput("error minicli") . "\n");

it('asserts that OutputHandler displays info with expected style', function () {
    $printer = getColorOutputHandler();
    $printer->info("info minicli");
})->expectOutputString("\n" . getInfoOutput("info minicli") . "\n");

it('asserts that OutputHandler displays success with expected style', function () {
    $printer = getColorOutputHandler();
    $printer->success("success minicli");
})->expectOutputString("\n" . getSuccessOutput("success minicli") . "\n");

it('asserts that OutputHandler allows changing theme', function () {
    $printer = getColorOutputHandler();
    $printer->clearFilters();
    $printer->registerFilter(new ColorOutputFilter(new UnicornTheme()));

    $printer->info("themed info minicli");
})->expectOutputString("\n" . getThemedOutput("themed info minicli"). "\n");

it('asserts that its possible to overwrite default styles', function () {
    $printer = getColorOutputHandler();
    $printer->clearFilters();

    $myCustomTheme = new DefaultTheme();
    $myCustomTheme->setStyle('default', [CLIColors::$FG_MAGENTA]);

    $printer->registerFilter(new ColorOutputFilter($myCustomTheme));
    $printer->display("custom theme");
})->expectOutputString("\n" . getThemedOutput("custom theme"). "\n");

it('asserts that custom styles can be used with the out method', function () {
    $printer = getColorOutputHandler();
    $printer->clearFilters();

    $myCustomTheme = new DefaultTheme();
    $myCustomTheme->setStyle('custom', [CLIColors::$FG_MAGENTA]);

    $printer->registerFilter(new ColorOutputFilter($myCustomTheme));
    $printer->out("custom theme", 'custom');
})->expectOutputString(getThemedOutput("custom theme"));

it('asserts that out method sets style to default when style is not passed', function () {
    $printer = getColorOutputHandler();
    $printer->out("testing minicli");
})->expectOutputString(getDefaultOutput("testing minicli"));
