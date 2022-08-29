<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;

class DraculaTheme extends DefaultTheme
{
    /**
     * get the colors
     *
     * @return array
     */
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_MAGENTA ],
            'alt'         => [ CLIColors::$FG_WHITE, CLIColors::$FG_MAGENTA ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_WHITE, CLIColors::$BG_RED ],
            'success'     => [ CliColors::$FG_GREEN ],
            'success_alt' => [ CLIColors::$FG_WHITE, CLIColors::$BG_GREEN ],
            'info'        => [ CLIColors::$FG_CYAN ],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_CYAN ]
        ];
    }
}
