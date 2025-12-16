<?php

declare(strict_types=1);

namespace Minicli\Log;

enum LogType: string
{
    case SINGLE = 'single';
    case DAILY = 'daily';
}
