<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;

class DraculaTheme extends DefaultTheme
{
    /**
     * get the colors
     *
     * @return array<string,array<int,string>>
     */
    public function themeColors(): array
    {
        return [
            'default' => [Foreground::MAGENTA->value],
            'alt' => [Foreground::WHITE->value, Background::MAGENTA->value],
            'error' => [Foreground::RED->value],
            'error_alt' => [Foreground::WHITE->value, Background::RED->value],
            'success' => [Foreground::GREEN->value],
            'success_alt' => [Foreground::WHITE->value, Background::GREEN->value],
            'info' => [Foreground::CYAN->value],
            'info_alt' => [Foreground::WHITE->value, Background::CYAN->value],
        ];
    }
}
