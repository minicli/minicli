<?php

declare(strict_types=1);

namespace Minicli\Logging;

enum LogType: string
{
    case SINGLE = 'single';
    case DAILY = 'daily';
}
