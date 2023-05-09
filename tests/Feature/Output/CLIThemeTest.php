<?php

use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\CLIColors;
use Minicli\Output\ThemeStyle;

it('asserts that themes set all default styles', function (DefaultTheme $theme) {
    expect($theme->getStyle('default'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('info'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('info_alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('error'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('error_alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('success'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->getStyle('success_alt'))->toBeInstanceOf(ThemeStyle::class);
})->with('themes');

it('asserts that default theme returns expected colors for default text', function () {
    $themeDefault = new DefaultTheme();

    expect($themeDefault->getStyle('default')->foreground)->toBe(CLIColors::$FG_WHITE);
});

it('asserts that missing styles in built-in themes are included from default theme', function (DefaultTheme $theme) {
    expect($theme->styles)
        ->toHaveKeys([
            'italic',
            'bold',
            'dim',
            'underline',
            'invert',
        ]);
})->with('themes');
