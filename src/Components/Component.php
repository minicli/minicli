<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Contracts\ComponentInterface;
use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\PrinterAdapterInterface;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Output\Filter\ColorOutputFilter;

abstract class Component implements ComponentInterface
{
    protected static ?OutputFilterInterface $filter = null;

    protected static ?PrinterAdapterInterface $printer = null;

    abstract public function output(): string;

    public function render(): void
    {
        echo $this->output();
    }

    public function setFilter(OutputFilterInterface $filter): self
    {
        self::$filter = $filter;

        return $this;
    }

    public function setPrinter(PrinterAdapterInterface $printer): self
    {
        self::$printer = $printer;

        return $this;
    }

    protected function filter(): OutputFilterInterface
    {
        if (! self::$filter instanceof OutputFilterInterface) {
            self::$filter = new ColorOutputFilter();
        }

        return self::$filter;
    }

    protected function printer(): PrinterAdapterInterface
    {
        if (! self::$printer instanceof PrinterAdapterInterface) {
            self::$printer = new DefaultPrinterAdapter();
        }

        return self::$printer;
    }
}
