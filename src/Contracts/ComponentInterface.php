<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface ComponentInterface
{
    /**
     * Outputs the string to show in the console.
     */
    public function output(): string;
}
