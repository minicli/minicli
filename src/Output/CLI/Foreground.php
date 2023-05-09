<?php

declare(strict_types=1);

namespace Minicli\Output\CLI;

enum Foreground: string
{
    case BLACK = '0;30';
    case WHITE = '1;37';
    case RED = '0;31';
    case GREEN = '0;32';
    case BLUE = '1;34';
    case CYAN = '0;36';
    case MAGENTA = '0;35';
    case YELLOW = '0;33';
}
