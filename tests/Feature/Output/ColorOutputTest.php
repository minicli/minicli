<?php

declare(strict_types=1);

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\OutputHandler;
use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\Theme\UnicornTheme;
use Minicli\Output\ThemeStyle;

/** Color Output Helpers */
function getColorOutputHandler(): OutputHandler
{
    $handler = new OutputHandler();
    $handler->registerFilter(new ColorOutputFilter());

    return $handler;
}

function getDefaultOutput($text): string
{
    return sprintf("\e[%sm%s\e[0m", Foreground::WHITE->value, $text);
}

function getAltOutput($text): string
{
    return sprintf("\e[%s;%sm%s\e[0m", Foreground::BLACK->value, Background::WHITE->value, $text);
}

function getErrorOutput($text): string
{
    return sprintf("\e[%sm%s\e[0m", Foreground::RED->value, $text);
}

function getInfoOutput($text): string
{
    return sprintf("\e[%sm%s\e[0m", Foreground::CYAN->value, $text);
}

function getSuccessOutput($text): string
{
    return sprintf("\e[%sm%s\e[0m", Foreground::GREEN->value, $text);
}

function getThemedOutput($text): string
{
    return sprintf("\e[%sm%s\e[0m", Foreground::MAGENTA->value, $text);
}

/** TESTS */
it('asserts that OutputHandler outputs correct style', function (): void {
    $printer = getColorOutputHandler();
    $printer->out('testing minicli', 'alt');
})->expectOutputString(getAltOutput('testing minicli'));

it('ColorOutput - asserts that OutputHandler outputs newline', function (): void {
    $printer = getColorOutputHandler();
    $printer->newline();
})->expectOutputString("\n");

it('ColorOutput - asserts that OutputHandler displays content wrapped in newlines', function (): void {
    $printer = getColorOutputHandler();
    $printer->display('testing minicli');
})->expectOutputString("\n" . getDefaultOutput('testing minicli') . "\n");

it('asserts that OutputHandler displays error with expected style', function (): void {
    $printer = getColorOutputHandler();
    $printer->error('error minicli');
})->expectOutputString("\n" . getErrorOutput('error minicli') . "\n");

it('asserts that OutputHandler displays info with expected style', function (): void {
    $printer = getColorOutputHandler();
    $printer->info('info minicli');
})->expectOutputString("\n" . getInfoOutput('info minicli') . "\n");

it('asserts that OutputHandler displays success with expected style', function (): void {
    $printer = getColorOutputHandler();
    $printer->success('success minicli');
})->expectOutputString("\n" . getSuccessOutput('success minicli') . "\n");

it('asserts that OutputHandler allows changing theme', function (): void {
    $printer = getColorOutputHandler();
    $printer->clearFilters();
    $printer->registerFilter(new ColorOutputFilter(new UnicornTheme()));

    $printer->info('themed info minicli');
})->expectOutputString("\n" . getThemedOutput('themed info minicli') . "\n");

it('asserts that its possible to overwrite default styles', function (): void {
    $printer = getColorOutputHandler();
    $printer->clearFilters();

    $myCustomTheme = new DefaultTheme();
    $myCustomTheme->setStyle('default', ThemeStyle::make(Foreground::MAGENTA->value));

    $printer->registerFilter(new ColorOutputFilter($myCustomTheme));
    $printer->display('custom theme');
})->expectOutputString("\n" . getThemedOutput('custom theme') . "\n");

it('asserts that custom styles can be used with the out method', function (): void {
    $printer = getColorOutputHandler();
    $printer->clearFilters();

    $myCustomTheme = new DefaultTheme();
    $myCustomTheme->setStyle('custom', ThemeStyle::make(Foreground::MAGENTA->value));

    $printer->registerFilter(new ColorOutputFilter($myCustomTheme));
    $printer->out('custom theme', 'custom');
})->expectOutputString(getThemedOutput('custom theme'));

it('asserts that out method sets style to default when style is not passed', function (): void {
    $printer = getColorOutputHandler();
    $printer->out('testing minicli');
})->expectOutputString(getDefaultOutput('testing minicli'));
