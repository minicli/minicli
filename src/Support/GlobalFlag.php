<?php

declare(strict_types=1);

namespace Minicli\Support;

enum GlobalFlag: string
{
    case HELP = '--help';
    case QUIET = '--quiet';
}
