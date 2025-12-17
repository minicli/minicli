<?php

declare(strict_types=1);

use Assets\Theme\CustomTheme;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\Theming\Themes\DaltonTheme;
use Minicli\Output\Theming\Themes\DefaultTheme;
use Minicli\Output\Theming\Themes\DraculaTheme;
use Minicli\Output\Theming\Themes\UnicornTheme;

it('asserts that ThemeHelper instantiates the Default theme')
    ->expect(fn (): Minicli\Contracts\ThemeInterface => new ThemeHelper()->getOutputFilter()->theme())
    ->toBeInstanceOf(DefaultTheme::class);

it('asserts that ThemeHelper instantiates the Unicorn theme')
    ->expect(fn (): Minicli\Contracts\ThemeInterface => new ThemeHelper('\Unicorn')->getOutputFilter()->theme())
    ->toBeInstanceOf(UnicornTheme::class);

it('asserts that ThemeHelper instantiates the Dalton theme')
    ->expect(fn (): Minicli\Contracts\ThemeInterface => new ThemeHelper('\Dalton')->getOutputFilter()->theme())
    ->toBeInstanceOf(DaltonTheme::class);

it('asserts that ThemeHelper instantiates the Dracula theme')
    ->expect(fn (): Minicli\Contracts\ThemeInterface => new ThemeHelper('\Dracula')->getOutputFilter()->theme())
    ->toBeInstanceOf(DraculaTheme::class);

it('asserts that ThemeHelper instantiates a custom theme')
    ->expect(fn (): Minicli\Contracts\ThemeInterface => new ThemeHelper('Assets\Theme\Custom')->getOutputFilter()->theme())
    ->toBeInstanceOf(CustomTheme::class);
