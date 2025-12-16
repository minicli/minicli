<?php

declare(strict_types=1);

namespace Minicli\Output;

interface OutputFilterInterface
{
    /**
     * output filter
     */
    public function filter(string $message, ?string $style = null): string;
}
