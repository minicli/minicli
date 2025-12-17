<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;

class DaltonTheme extends DefaultTheme
{
    /**
     * get the colors
     *
     * @return array<string, array<int, string>>
     */
    public function themeColors(): array
    {
        return [
            'default' => [Foreground::YELLOW->value],
            'alt' => [Foreground::BLACK->value, Background::YELLOW->value],
            'error' => [Foreground::RED->value],
            'error_alt' => [Foreground::WHITE->value, Background::RED->value],
            'success' => [Foreground::CYAN->value],
            'success_alt' => [Foreground::BLACK->value, Background::CYAN->value],
            'info' => [Foreground::MAGENTA->value],
            'info_alt' => [Foreground::WHITE->value, Background::MAGENTA->value],
        ];
    }
}
