<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface OutputFilterInterface
{
    public function filter(string $message, ?string $style = null): string;
}
