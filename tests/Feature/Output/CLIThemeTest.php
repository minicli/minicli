<?php

declare(strict_types=1);

use Minicli\Output\CLI\Foreground;
use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\ThemeStyle;

it('asserts that themes set all default styles', function (DefaultTheme $theme): void {
    expect($theme->style('default'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('info'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('info_alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('error'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('error_alt'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('success'))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style('success_alt'))->toBeInstanceOf(ThemeStyle::class);
})->with('themes');

it('asserts that default theme returns expected colors for default text')
    ->expect(fn (): string => new DefaultTheme()->style('default')->foreground)
    ->toBe(Foreground::WHITE->value);

it('asserts that missing styles in built-in themes are included from default theme', function (DefaultTheme $theme): void {
    expect($theme->config->italic)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->bold)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->dim)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->underline)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->invert)->toBeInstanceOf(ThemeStyle::class);
})->with('themes');
