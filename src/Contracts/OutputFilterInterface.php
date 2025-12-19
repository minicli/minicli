<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\Output\Theming\StyleType;

interface OutputFilterInterface
{
    /**
     * @param  array<StyleType>  $styles
     */
    public function filter(string $message, array $styles = []): string;
}
