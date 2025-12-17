<?php

declare(strict_types=1);

namespace Minicli\Output\Theming;

enum StyleType: string
{
    case DEFAULT = 'default';
    case ALT = 'alt';
    case ERROR = 'error';
    case ERROR_ALT = 'error_alt';
    case WARNING = 'warning';
    case WARNING_ALT = 'warning_alt';
    case SUCCESS = 'success';
    case SUCCESS_ALT = 'success_alt';
    case INFO = 'info';
    case INFO_ALT = 'info_alt';
    case BOLD = 'bold';
    case DIM = 'dim';
    case ITALIC = 'italic';
    case UNDERLINE = 'underline';
    case INVERT = 'invert';
}
