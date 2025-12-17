<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;
use Minicli\Output\ThemeConfig;
use Minicli\Output\ThemeStyle;

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
