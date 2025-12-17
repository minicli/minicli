<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\Foreground;

class UnicornTheme extends DefaultTheme
{
    /**
     * get theme colors
     *
     * @return array<string,array<int,string>>
     */
    public function themeColors(): array
    {
        return [
            'default' => [Foreground::CYAN->value],
            'alt' => [Foreground::BLACK->value, Background::CYAN->value],
            'error' => [Foreground::RED->value],
            'error_alt' => [Foreground::CYAN->value, Background::RED->value],
            'success' => [Foreground::GREEN->value],
            'success_alt' => [Foreground::BLACK->value, Background::GREEN->value],
            'info' => [Foreground::MAGENTA->value],
            'info_alt' => [Foreground::WHITE->value, Background::MAGENTA->value],
        ];
    }
}
