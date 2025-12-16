<?php

declare(strict_types=1);

namespace Minicli\Console;

enum ExitCode: int
{
    case Success = 0;
    case Failure = 1;
    case Invalid = 2;
}
