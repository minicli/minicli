<?php

use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\Theme\UnicornTheme;
use Assets\Theme\CustomTheme;

it('asserts that ThemeHelper instantiates the right existing theme', function () {
    $helper = new ThemeHelper('\Unicorn');
    $filter = $helper->getOutputFilter();

    assertInstanceOf(UnicornTheme::class, $filter->getTheme());
});

it('asserts that ThemeHelper instantiates a custom theme', function () {
    $helper = new ThemeHelper('Assets\Theme\Custom');
    $filter = $helper->getOutputFilter();

    assertInstanceOf(CustomTheme::class, $filter->getTheme());
});
