<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Contracts\OutputFilterInterface;
use Minicli\Output\Theming\StyleType;

class SimpleOutputFilter implements OutputFilterInterface
{
    public function filter(string $message, ?StyleType $style = null): string
    {
        return $message;
    }
}
