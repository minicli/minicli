<?php

declare(strict_types=1);

namespace Minicli\Output\Theming\Themes;

use Minicli\Output\Theming\Background;
use Minicli\Output\Theming\Foreground;
use Minicli\Output\Theming\ThemeConfig;
use Minicli\Output\Theming\ThemeStyle;

class DraculaTheme extends DefaultTheme
{
    public function themeConfig(): ThemeConfig
    {
        return ThemeConfig::make(
            default: ThemeStyle::make(Foreground::MAGENTA),
            alt: ThemeStyle::make(Foreground::WHITE, Background::MAGENTA),
            error: ThemeStyle::make(Foreground::RED),
            error_alt: ThemeStyle::make(Foreground::WHITE, Background::RED),
            success: ThemeStyle::make(Foreground::GREEN),
            success_alt: ThemeStyle::make(Foreground::WHITE, Background::GREEN),
            info: ThemeStyle::make(Foreground::CYAN),
            info_alt: ThemeStyle::make(Foreground::WHITE, Background::CYAN),
        );
    }
}
