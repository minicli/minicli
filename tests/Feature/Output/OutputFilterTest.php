<?php

use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\CLIColors;
use Minicli\Output\Theme\UnicornTheme;
use Minicli\Output\Filter\TimestampOutputFilter;

it('asserts that SimpleOutputFilter returns unstyled content', function () {
    $simple = new SimpleOutputFilter();

    $text = "My content";
    $this->assertEquals($text, $simple->filter($text));
});

it('asserts that ColorOutputFilter returns styled content with default theme', function () {
    $color = new ColorOutputFilter();

    $text = "My content";
    $styled = $color->filter($text);

    $expected =  sprintf("\e[%sm%s\e[0m", CLIColors::$FG_WHITE, $text);
    $this->assertEquals($expected, $styled);
});

it('asserts that ColorOutputFilter sets theme correctly and formats with style', function () {
    $color = new ColorOutputFilter();
    $color->setTheme(new UnicornTheme());

    $this->assertInstanceOf(UnicornTheme::class, $color->getTheme());

    $text = "My content";
    $styled = $color->filter($text, 'info');

    $expected =  sprintf("\e[%sm%s\e[0m", CLIColors::$FG_MAGENTA, $text);
    $this->assertEquals($expected, $styled);
});

it('asserts that TimestampOutputFilter adds timestamp to messages', function () {
    $time = new TimestampOutputFilter();

    $message = "test timestamp";
    $today = (new DateTime())->format('Y-m-d');

    $this->assertStringContainsString($today, $time->filter($message));
    $this->assertStringContainsString($message, $time->filter($message));
});
