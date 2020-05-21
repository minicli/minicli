<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CliThemeInterface;
use Minicli\Output\CliColors;

class UnicornCliTheme extends DefaultCliTheme
{
    public function loadColors()
    {
        $this->default     = [ CliColors::$FG_CYAN ];
        $this->alt         = [ CliColors::$FG_BLACK, CliColors::$BG_CYAN ];
        $this->error       = [ CliColors::$FG_RED ];
        $this->error_alt   = [ CliColors::$FG_CYAN, CliColors::$BG_RED ];
        $this->success     = [ CliColors::$FG_GREEN ];
        $this->success_alt = [ CliColors::$FG_BLACK, CliColors::$BG_GREEN ];
        $this->info        = [ CliColors::$FG_MAGENTA];
        $this->info_alt    = [ CliColors::$FG_WHITE, CliColors::$BG_MAGENTA ];
    }
}