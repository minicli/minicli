<?php

declare(strict_types=1);

namespace Minicli\Output\Theming\Themes;

use Minicli\Output\Theming\Background;
use Minicli\Output\Theming\Foreground;
use Minicli\Output\Theming\ThemeConfig;
use Minicli\Output\Theming\ThemeStyle;

class DaltonTheme extends DefaultTheme
{
    public function themeConfig(): ThemeConfig
    {
        return ThemeConfig::make(
            default: ThemeStyle::make(Foreground::YELLOW),
            alt: ThemeStyle::make(Foreground::BLACK, Background::YELLOW),
            error: ThemeStyle::make(Foreground::RED),
            error_alt: ThemeStyle::make(Foreground::WHITE, Background::RED),
            success: ThemeStyle::make(Foreground::CYAN),
            success_alt: ThemeStyle::make(Foreground::BLACK, Background::CYAN),
            info: ThemeStyle::make(Foreground::MAGENTA),
            info_alt: ThemeStyle::make(Foreground::WHITE, Background::MAGENTA),
        );
    }
}
