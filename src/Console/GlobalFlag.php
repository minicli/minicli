<?php

declare(strict_types=1);

namespace Minicli\Console;

enum GlobalFlag: string
{
    case HELP = '--help';
    case QUIET = '--quiet';
    case PROFILE = '--profile';
}
