<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class SimpleOutputFilter implements OutputFilterInterface
{
    /**
     * simple filter
     *
     * @param string $message
     * @param string|null $style
     * @return string
     */
    public function filter(string $message, ?string $style = null): string
    {
        return $message;
    }
}
