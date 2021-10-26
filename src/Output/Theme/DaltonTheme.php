<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;

class DaltonTheme extends DefaultTheme
{
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_YELLOW ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_YELLOW ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_WHITE, CLIColors::$BG_RED ],
            'success'     => [ CliColors::$FG_CYAN ],
            'success_alt' => [ CLIColors::$FG_BLACK, CLIColors::$BG_CYAN ],
            'info'        => [ CLIColors::$FG_MAGENTA],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_MAGENTA ]
        ];
    }
}