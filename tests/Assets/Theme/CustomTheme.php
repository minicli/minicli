<?php

declare(strict_types=1);

namespace Assets\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;
use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\ThemeConfig;
use Minicli\Output\ThemeStyle;

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
