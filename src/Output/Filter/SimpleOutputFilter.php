<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Contracts\OutputFilterInterface;
use Minicli\Output\Theming\StyleType;

class SimpleOutputFilter implements OutputFilterInterface
{
    /**
     * @param  array<StyleType>  $styles
     */
    public function filter(string $message, array $styles = []): string
    {
        return $message;
    }
}
