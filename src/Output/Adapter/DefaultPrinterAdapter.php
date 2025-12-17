<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Contracts\PrinterAdapterInterface;

class DefaultPrinterAdapter implements PrinterAdapterInterface
{
    public function out(string $message): string
    {
        return $message;
    }
}
