<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\Output\Theming\StyleType;

interface OutputFilterInterface
{
    public function filter(string $message, ?StyleType $style = null): string;
}
