<?php

declare(strict_types=1);

namespace Minicli\Output;

class CLIColors
{
    public static string $FG_BLACK = '0;30';
    public static string $FG_WHITE = '1;37';
    public static string $FG_RED = '0;31';
    public static string $FG_GREEN = '0;32';
    public static string $FG_BLUE = '1;34';
    public static string $FG_CYAN = '0;36';
    public static string $FG_MAGENTA = '0;35';
    public static string $FG_YELLOW = '0;33';

    public static string $BG_BLACK = '40';
    public static string $BG_RED = '41';
    public static string $BG_GREEN = '42';
    public static string $BG_BLUE = '44';
    public static string $BG_CYAN = '46';
    public static string $BG_WHITE = '47';
    public static string $BG_MAGENTA = '45';
    public static string $BG_YELLOW = '43';

    public static string $BOLD = '1';
    public static string $DIM = '2';
    public static string $ITALIC = '3';
    public static string $UNDERLINE = '4';
    public static string $INVERT = '7';
}
