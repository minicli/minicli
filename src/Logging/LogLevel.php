<?php

declare(strict_types=1);

namespace Minicli\Logging;

enum LogLevel: string
{
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
    case DEBUG = 'DEBUG';
}
