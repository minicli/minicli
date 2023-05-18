<?php

declare(strict_types=1);

use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\Theme\DraculaTheme;
use Minicli\Output\Theme\UnicornTheme;
use Assets\Theme\CustomTheme;
use Minicli\Output\Theme\DaltonTheme;

it('asserts that ThemeHelper instantiates the Default theme')
    ->expect(fn () => (new ThemeHelper())->getOutputFilter()->getTheme())
    ->toBeInstanceOf(DefaultTheme::class);

it('asserts that ThemeHelper instantiates the Unicorn theme')
    ->expect(fn () => (new ThemeHelper('\Unicorn'))->getOutputFilter()->getTheme())
    ->toBeInstanceOf(UnicornTheme::class);

it('asserts that ThemeHelper instantiates the Dalton theme')
    ->expect(fn () => (new ThemeHelper('\Dalton'))->getOutputFilter()->getTheme())
    ->toBeInstanceOf(DaltonTheme::class);

it('asserts that ThemeHelper instantiates the Dracula theme')
    ->expect(fn () => (new ThemeHelper('\Dracula'))->getOutputFilter()->getTheme())
    ->toBeInstanceOf(DraculaTheme::class);

it('asserts that ThemeHelper instantiates a custom theme')
    ->expect(fn () => (new ThemeHelper('Assets\Theme\Custom'))->getOutputFilter()->getTheme())
    ->toBeInstanceOf(CustomTheme::class);
