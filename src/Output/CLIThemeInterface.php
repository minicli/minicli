<?php

declare(strict_types=1);

namespace Minicli\Output;

interface CLIThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param string $name The name of the style
     * @return array An array containing FG color and optionally BG color
     */
    public function getStyle(string $name): array;
}
