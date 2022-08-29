<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;

class DraculaTheme extends DefaultTheme
{
    /**
     * get theme colors
     *
     * @return array
     */
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_MAGENTA ],
            'alt'         => [ CLIColors::$FG_WHITE, CLIColors::$BG_MAGENTA ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_MAGENTA, CLIColors::$BG_RED ],
            'success'     => [ CLIColors::$FG_GREEN ],
            'success_alt' => [ CLIColors::$FG_BLACK, CLIColors::$BG_GREEN ],
            'info'        => [ CLIColors::$FG_CYAN],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_CYAN ]
        ];
    }
}
