<?php

declare(strict_types=1);

namespace Minicli\Output;

use Minicli\Output\OutputFilterInterface;

interface CLIThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param string $name The name of the style
     * @return ThemeStyle
     */
    public function getStyle(string $name): ThemeStyle;

    /**
     * Initialize and return an OutputFilter for our theme class.
     *
     * @return OutputFilterInterface
     */
    public function getOutputFilter(): OutputFilterInterface;
}
