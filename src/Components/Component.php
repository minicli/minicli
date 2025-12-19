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

    protected static bool $quiet = false;

    abstract public function output(): string;

    public static function setFilter(OutputFilterInterface $filter): void
    {
        self::$filter = $filter;
    }

    public static function setPrinter(PrinterAdapterInterface $printer): void
    {
        self::$printer = $printer;
    }

    public static function setQuiet(bool $quiet): void
    {
        self::$quiet = $quiet;
    }

    public function render(): void
    {
        if (self::$quiet) {
            return;
        }

        echo $this->output();
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
