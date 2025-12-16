<?php

declare(strict_types=1);

namespace Minicli\Output;

interface PrinterAdapterInterface
{
    /**
     * output method
     */
    public function out(string $message): string;
}
