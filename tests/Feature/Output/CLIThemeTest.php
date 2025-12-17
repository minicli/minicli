<?php

declare(strict_types=1);

use Minicli\Output\Theming\Foreground;
use Minicli\Output\Theming\StyleType;
use Minicli\Output\Theming\Themes\DefaultTheme;
use Minicli\Output\Theming\ThemeStyle;

it('asserts that themes set all default styles', function (DefaultTheme $theme): void {
    expect($theme->style(StyleType::DEFAULT))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::ALT))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::INFO))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::INFO_ALT))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::ERROR))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::ERROR_ALT))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::SUCCESS))->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->style(StyleType::SUCCESS_ALT))->toBeInstanceOf(ThemeStyle::class);
})->with('themes');

it('asserts that default theme returns expected colors for default text')
    ->expect(fn (): string => new DefaultTheme()->style(StyleType::DEFAULT)->foreground->value)
    ->toBe(Foreground::WHITE->value);

it('asserts that missing styles in built-in themes are included from default theme', function (DefaultTheme $theme): void {
    expect($theme->config->italic)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->bold)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->dim)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->underline)->toBeInstanceOf(ThemeStyle::class)
        ->and($theme->config->invert)->toBeInstanceOf(ThemeStyle::class);
})->with('themes');
