<?php

use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\Theme\UnicornTheme;
use Assets\Theme\CustomTheme;
use Minicli\Output\Theme\DaltonTheme;

it('asserts that ThemeHelper instantiates the right existing theme', function () {
    $helper = new ThemeHelper('\Unicorn');
    $filter = $helper->getOutputFilter();

    $this->assertInstanceOf(UnicornTheme::class, $filter->getTheme());
});

it('asserts that ThemeHelper instantiates Dalton theme', function () {
    $helper = new ThemeHelper('\Dalton');
    $filter = $helper->getOutputFilter();

    $this->assertInstanceOf(DaltonTheme::class, $filter->getTheme());
});

it('asserts that ThemeHelper instantiates a custom theme', function () {
    $helper = new ThemeHelper('Assets\Theme\Custom');
    $filter = $helper->getOutputFilter();

    $this->assertInstanceOf(CustomTheme::class, $filter->getTheme());
});
