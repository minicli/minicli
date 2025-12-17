<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\Output\Theming\StyleType;

interface OutputFilterInterface
{
    /**
     * @param  array<StyleType>  $formats
     */
    public function filter(string $message, ?StyleType $style = null, array $formats = []): string;
}
