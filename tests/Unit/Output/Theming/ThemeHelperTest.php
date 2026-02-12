<?php

declare(strict_types=1);

use Assets\Theme\CustomTheme;
use Minicli\Output\Theming\ThemeHelper;
use Minicli\Output\Theming\Themes\DefaultTheme;

it('uses default theme when no theme class is provided', function (): void {
    $theme = new ThemeHelper()->getOutputFilter()->theme();

    expect($theme)->toBeInstanceOf(DefaultTheme::class);
});

it('uses custom theme when valid class is provided', function (): void {
    $theme = new ThemeHelper(CustomTheme::class)->getOutputFilter()->theme();

    expect($theme)->toBeInstanceOf(CustomTheme::class);
});

it('falls back to default theme for invalid class name', function (): void {
    $theme = new ThemeHelper('Missing\\Theme\\ClassName')->getOutputFilter()->theme();

    expect($theme)->toBeInstanceOf(DefaultTheme::class);
});
