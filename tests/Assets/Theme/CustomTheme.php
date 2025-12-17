<?php

declare(strict_types=1);

namespace Assets\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;
use Minicli\Output\Theme\DefaultTheme;

class CustomTheme extends DefaultTheme
{
    public function themeColors(): array
    {
        return [
            'default' => [Foreground::CYAN->value],
            'alt' => [Foreground::BLACK->value, Background::CYAN->value],
            'info' => [Foreground::MAGENTA->value],
            'info_alt' => [Foreground::WHITE->value, Background::MAGENTA->value],
        ];
    }
}
