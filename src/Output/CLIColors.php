<?php

declare(strict_types=1);

namespace Minicli\Output;

class CLIColors
{
    public static $FG_BLACK = '0;30';
    public static $FG_WHITE = '1;37';
    public static $FG_RED = '0;31';
    public static $FG_GREEN = '0;32';
    public static $FG_BLUE = '1;34';
    public static $FG_CYAN = '0;36';
    public static $FG_MAGENTA = '0;35';
    public static $FG_YELLOW = '0;33';

    public static $BG_BLACK = '40';
    public static $BG_RED = '41';
    public static $BG_GREEN = '42';
    public static $BG_BLUE = '44';
    public static $BG_CYAN = '46';
    public static $BG_WHITE = '47';
    public static $BG_MAGENTA = '45';
    public static $BG_YELLOW = '43';

    public static $BOLD = '1';
    public static $DIM = '2';
    public static $ITALIC = '3';
    public static $UNDERLINE = '4';
    public static $INVERT = '7';
}
