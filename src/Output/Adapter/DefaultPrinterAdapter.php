<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Contracts\PrinterAdapterInterface;

class DefaultPrinterAdapter implements PrinterAdapterInterface
{
    /**
     * output
     */
    public function out(string $message): string
    {
        return $message;
    }
}
