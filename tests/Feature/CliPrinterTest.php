<?php

use Minicli\Output\Theme\DefaultCliTheme;
use Minicli\Output\Theme\UnicornCliTheme;
use Minicli\Output\CliColors;
use Minicli\Output\CliPrinter;

it('asserts that CliPrinter sets default theme upon instantiation', function () {
    $printer = new CliPrinter();

    assertInstanceOf(DefaultCliTheme::class, $printer->theme);
});

it('asserts that CliPrinter correctly sets custom theme', function () {
    $printer = new CliPrinter();
    $printer->setTheme(new UnicornCliTheme());

    assertInstanceOf(UnicornCliTheme::class, $printer->theme);
});

it('asserts that CliPrinter outputs in color', function () {
    $printer = new CliPrinter();

    $text = $printer->format("testing minicli", "alt");
    $expected = sprintf("\e[%s;%sm%s\e[0m", CliColors::$FG_BLACK, CliColors::$BG_WHITE, "testing minicli");

    assertEquals($expected, $text);
});
