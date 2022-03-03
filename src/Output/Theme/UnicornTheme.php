<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;

class UnicornTheme extends DefaultTheme
{
    /**
     * get theme colors
     *
     * @return array
     */
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_CYAN ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_CYAN ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_CYAN, CLIColors::$BG_RED ],
            'success'     => [ CliColors::$FG_GREEN ],
            'success_alt' => [ CLIColors::$FG_BLACK, CLIColors::$BG_GREEN ],
            'info'        => [ CLIColors::$FG_MAGENTA],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_MAGENTA ]
        ];
    }
}
