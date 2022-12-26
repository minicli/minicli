<?php

namespace Minicli\Output;

use Minicli\Output\OutputFilterInterface;

interface CLIThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     * @param string $name The name of the style
     * @return array An array containing FG color and optionally BG color
     */
    public function getStyle(string $name);

    /**
     * Initialize and return an OutputFilter for our theme class.
     * @return OutputFilterInterface
     */
    public function getOutputFilter();
}
