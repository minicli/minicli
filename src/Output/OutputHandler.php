<?php

namespace Minicli\Output;

use Minicli\App;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\ServiceInterface;

class OutputHandler implements ServiceInterface
{
    /** @var PrinterAdapterInterface */
    protected $printer_adapter;

    /** @var array */
    protected $output_filters = [];

    /**
     * OutputHandler constructor.
     * @param PrinterAdapterInterface $printer
     */
    public function __construct(PrinterAdapterInterface $printer = null)
    {
        $this->printer_adapter = $printer ?? new DefaultPrinterAdapter();
    }

    public function registerFilter(OutputFilterInterface $filter)
    {
        $this->output_filters[] = $filter;
    }

    public function clearFilters()
    {
        $this->output_filters = [];
    }

    public function load(App $app)
    {
        return true;
    }

    public function filterOutput($message, $style = null)
    {
        /** @var OutputFilterInterface $filter */

        foreach ($this->output_filters as $filter) {
            $message = $filter->filter($message, $style);
        }

        return $message;
    }

    public function out($message, $style = null)
    {
        $this->printer_adapter->out($this->filterOutput($message, $style));
    }

    public function rawOutput($content)
    {
        $this->printer_adapter->out($content);
    }

    public function newline()
    {
        $this->rawOutput("\n");
    }

    /**
     * @param string $message
     * @return void
     */
    public function display($message, $alt = false)
    {
        $this->newline();
        $this->out($message, $alt ? "alt" : "default");
        $this->newline();
        $this->newline();
    }

    /**
     * @param string $message
     * @return void
     */
    public function error($message, $alt = false)
    {
        $this->newline();
        $this->out($message, $alt ? "error_alt" : "error");
        $this->newline();
    }

    /**
     * @param string $message
     * @return void
     */
    public function info($message, $alt = false)
    {
        $this->newline();
        $this->out($message, $alt ? "info_alt" : "info");
        $this->newline();
    }

    /**
     * @param string $message
     * @return void
     */
    public function success($message, $alt = false)
    {
        $this->newline();
        $this->out($message, $alt ? "success_alt" : "success");
        $this->newline();
    }

    /**
     * @param array $table
     * @param int $min_col_size
     * @param bool $with_header
     */
    public function printTable(array $table, $min_col_size = 10, $with_header = true, $spacing = true)
    {
        $first = true;
            $helper = new TableHelper();

        if ($spacing) {
            $this->newline();
        }

        foreach ($table as $index => $row) {

            if ($first && $with_header) {
                array_map(function ($item) {
                    return strtoupper($item);
                }, $row);
            }

            $this->out($helper->getRow($table, $index, $min_col_size));
            $first = false;
        }

        if ($spacing) {
            $this->newline();
        }
    }
}