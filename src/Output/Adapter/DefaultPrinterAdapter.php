<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class DefaultPrinterAdapter implements PrinterAdapterInterface
{
    /**
     * output
     *
     * @param string $message
     * @return string
     */
    public function out(string $message): string
    {
        return $message;
    }
}
