<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;
use Minicli\Output\CLIThemeInterface;
use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\OutputFilterInterface;
use Minicli\Output\ThemeStyle;

class RawTheme implements CLIThemeInterface
{
    /**
     * @inheritdoc
     */
    public function getStyle(string $name): ThemeStyle
    {
        return new ThemeStyle(CLIColors::$FG_WHITE);
    }

    /**
     * @inheritdoc
     */
    public function getOutputFilter(): OutputFilterInterface
    {
        return new SimpleOutputFilter();
    }
}
