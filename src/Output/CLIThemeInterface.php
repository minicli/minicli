<?php

declare(strict_types=1);

namespace Minicli\Output;

interface CLIThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param string $name The name of the style
     * @return ThemeStyle
     */
    public function getStyle(string $name): ThemeStyle;
}
