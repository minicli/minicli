<?php

use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\CLIColors;
use Minicli\Output\Theme\UnicornTheme;
use Minicli\Output\Filter\TimestampOutputFilter;

it('asserts that SimpleOutputFilter returns unstyled content')
    ->expect((new SimpleOutputFilter())->filter("My content"))
    ->toBe("My content");

it('asserts that ColorOutputFilter returns styled content with default theme')
    ->expect((new ColorOutputFilter())->filter("My content"))
    ->toBe(sprintf("\e[%sm%s\e[0m", CLIColors::$FG_WHITE, "My content"));

it('asserts that ColorOutputFilter sets theme correctly and formats with style', function () {
    $color = new ColorOutputFilter();
    $color->setTheme(new UnicornTheme());

    $text = "My content";
    $styled = $color->filter($text, 'info');
    $expected =  sprintf("\e[%sm%s\e[0m", CLIColors::$FG_MAGENTA, $text);

    expect($color->getTheme())->toBeInstanceOf(UnicornTheme::class)
        ->and($styled)->toBe($expected);
});

it('asserts that TimestampOutputFilter adds timestamp to messages')
    ->expect((new TimestampOutputFilter())->filter("test timestamp"))
    ->toContain((new DateTime())->format('Y-m-d'))
    ->toContain("test timestamp");

it('asserts that TimestampOutputFilter adds formatted timestamp to messages')
    ->expect((new TimestampOutputFilter())->filter("test timestamp", 'm/d/Y'))
    ->toContain((new DateTime())->format('m/d/Y'))
    ->toContain("test timestamp");
