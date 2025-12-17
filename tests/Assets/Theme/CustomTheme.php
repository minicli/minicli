<?php

declare(strict_types=1);

namespace Assets\Theme;

use Minicli\Output\Theming\Background;
use Minicli\Output\Theming\Foreground;
use Minicli\Output\Theming\ThemeConfig;
use Minicli\Output\Theming\Themes\DefaultTheme;
use Minicli\Output\Theming\ThemeStyle;

class CustomTheme extends DefaultTheme
{
    public function themeConfig(): ThemeConfig
    {
        return ThemeConfig::make(
            default: ThemeStyle::make(Foreground::CYAN),
            alt: ThemeStyle::make(Foreground::BLACK, Background::CYAN),
            info: ThemeStyle::make(Foreground::MAGENTA),
            info_alt: ThemeStyle::make(Foreground::WHITE, Background::MAGENTA),
        );
    }
}
