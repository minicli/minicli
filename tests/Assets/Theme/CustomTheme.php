<?php

namespace Assets\Theme;

use Minicli\Output\CLIColors;
use Minicli\Output\Theme\DefaultTheme;

class CustomTheme extends DefaultTheme
{
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_CYAN ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_CYAN ],
            'info'        => [ CLIColors::$FG_MAGENTA],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_MAGENTA ]
        ];
    }
}
