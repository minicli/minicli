<?php

use Minicli\Output\Theme\DefaultCliTheme;
use Minicli\Output\Theme\UnicornCliTheme;
use Minicli\Output\CliColors;

it('asserts that Default CLI theme sets all default styles', function () {
   $theme = new DefaultCliTheme();

   assertIsArray($theme->getDefault());
   assertIsArray($theme->getAlt());
   assertIsArray($theme->getError());
   assertIsArray($theme->getErrorAlt());
   assertIsArray($theme->getSuccess());
   assertIsArray($theme->getSuccessAlt());
   assertIsArray($theme->getInfo());
   assertIsArray($theme->getInfoAlt());
});

it('asserts that default theme returns expected colors for default text', function () {
    $theme_default = new DefaultCliTheme();

    assertContains(CliColors::$FG_WHITE, $theme_default->getDefault());
});

it('asserts that Unicorn CLI theme sets all default styles', function () {
    $theme = new UnicornCliTheme();

    assertIsArray($theme->getDefault());
    assertIsArray($theme->getAlt());
    assertIsArray($theme->getError());
    assertIsArray($theme->getErrorAlt());
    assertIsArray($theme->getSuccess());
    assertIsArray($theme->getSuccessAlt());
    assertIsArray($theme->getInfo());
    assertIsArray($theme->getInfoAlt());
});
