<?php

use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\Theme\UnicornTheme;
use Minicli\Output\CLIColors;
use Minicli\Output\Theme\DaltonTheme;

it('asserts that Default CLI theme sets all default styles', function () {
    $theme = new DefaultTheme();

    $this->assertIsArray($theme->getStyle('default'));
    $this->assertIsArray($theme->getStyle('alt'));
    $this->assertIsArray($theme->getStyle('info'));
    $this->assertIsArray($theme->getStyle('info_alt'));
    $this->assertIsArray($theme->getStyle('error'));
    $this->assertIsArray($theme->getStyle('error_alt'));
    $this->assertIsArray($theme->getStyle('success'));
    $this->assertIsArray($theme->getStyle('success_alt'));
});

it('asserts that default theme returns expected colors for default text', function () {
    $themeDefault = new DefaultTheme();

    $this->assertContains(CLIColors::$FG_WHITE, $themeDefault->getStyle('default'));
});

it('asserts that Unicorn CLI theme sets all default styles', function () {
    $theme = new UnicornTheme();

    $this->assertIsArray($theme->getStyle('default'));
    $this->assertIsArray($theme->getStyle('alt'));
    $this->assertIsArray($theme->getStyle('info'));
    $this->assertIsArray($theme->getStyle('info_alt'));
    $this->assertIsArray($theme->getStyle('error'));
    $this->assertIsArray($theme->getStyle('error_alt'));
    $this->assertIsArray($theme->getStyle('success'));
    $this->assertIsArray($theme->getStyle('success_alt'));
});

it('asserts that missing styles in Unicorn CLI theme are included from default theme', function () {
    $theme = new UnicornTheme();

    $this->assertArrayHasKey('italic', $theme->styles);
    $this->assertArrayHasKey('bold', $theme->styles);
    $this->assertArrayHasKey('dim', $theme->styles);
    $this->assertArrayHasKey('underline', $theme->styles);
    $this->assertArrayHasKey('invert', $theme->styles);
});

it('asserts that Dalton CLI theme sets all default styles', function () {
    $theme = new DaltonTheme();

    $this->assertIsArray($theme->getStyle('default'));
    $this->assertIsArray($theme->getStyle('alt'));
    $this->assertIsArray($theme->getStyle('info'));
    $this->assertIsArray($theme->getStyle('info_alt'));
    $this->assertIsArray($theme->getStyle('error'));
    $this->assertIsArray($theme->getStyle('error_alt'));
    $this->assertIsArray($theme->getStyle('success'));
    $this->assertIsArray($theme->getStyle('success_alt'));
});

it('asserts that missing styles in Dalton CLI theme are included from default theme', function () {
    $theme = new DaltonTheme();

    $this->assertArrayHasKey('italic', $theme->styles);
    $this->assertArrayHasKey('bold', $theme->styles);
    $this->assertArrayHasKey('dim', $theme->styles);
    $this->assertArrayHasKey('underline', $theme->styles);
    $this->assertArrayHasKey('invert', $theme->styles);
});
