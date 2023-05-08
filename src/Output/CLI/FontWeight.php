<?php

declare(strict_types=1);

namespace Minicli\Output\CLI;

enum FontWeight: string
{
    case DEFAULT = '0';
    case BOLD = '1';
    case DIM = '2';
    case ITALIC = '3';
    case UNDERLINE = '4';
    case INVERT = '7';
}
