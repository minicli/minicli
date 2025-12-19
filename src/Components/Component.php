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

    protected function stringWidth(string $text): int
    {
        return mb_strlen($text);
    }

    protected function visualWidth(string $text): int
    {
        // Strip ANSI color codes before calculating width
        $stripped = preg_replace('/\033\[[0-9;]*m/', '', $text);

        return mb_strlen($stripped ?? $text);
    }

    /**
     * @param  array<string>  $strings
     */
    protected function maxStringWidth(array $strings): int
    {
        $maxWidth = 0;

        foreach ($strings as $string) {
            $width = $this->stringWidth($string);
            if ($width > $maxWidth) {
                $maxWidth = $width;
            }
        }

        return $maxWidth;
    }

    protected function terminalWidth(): int
    {
        // Try to get terminal width from tput
        $width = @exec('tput cols 2>/dev/null');
        if (! in_array($width, [false, null, ''], true)) {
            return (int) $width;
        }

        // Fallback to COLUMNS environment variable
        $columns = getenv('COLUMNS');
        if ($columns !== false && $columns !== '') {
            return (int) $columns;
        }

        // Default fallback width
        return 80;
    }
}
