<?php

namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class DefaultPrinterAdapter implements PrinterAdapterInterface
{
    public function out($message)
    {
        echo $message;
    }
}
