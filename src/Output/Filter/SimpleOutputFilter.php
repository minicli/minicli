<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class SimpleOutputFilter implements OutputFilterInterface
{
    /**
     * simple filter
     */
    public function filter(string $message, ?string $style = null): string
    {
        return $message;
    }
}
