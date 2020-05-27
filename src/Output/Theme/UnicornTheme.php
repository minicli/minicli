<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CLITheme;
use Minicli\Output\CLIColors;

class UnicornTheme extends CLITheme
{
    public function getDefaultColors()
    {
        $styles = [
            'default'     => [ CLIColors::$FG_CYAN ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_CYAN ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_CYAN, CLIColors::$BG_RED ],
            'success'     => [ CliColors::$FG_GREEN ],
            'success_alt' => [ CLIColors::$FG_BLACK, CLIColors::$BG_GREEN ],
            'info'        => [ CLIColors::$FG_MAGENTA],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_MAGENTA ]
        ];

        return array_merge(parent::getDefaultColors(), $styles); // Any styles not defined here, will use the default styles.
    }
}
