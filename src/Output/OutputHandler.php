<?php

declare(strict_types=1);

namespace Minicli\Output;

use Minicli\App;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Output\Helper\TableHelper;
use Minicli\ServiceInterface;

class OutputHandler implements ServiceInterface
{
    /**
     * printer adapter
     *
     * @var PrinterAdapterInterface
     */
    protected PrinterAdapterInterface $printerAdapter;

    /**
     * output filters
     *
     * @var array
     */
    protected array $outputFilters = [];

    /**
     * OutputHandler constructor
     *
     * @param PrinterAdapterInterface|null $printer
     */
    public function __construct(?PrinterAdapterInterface $printer = null)
    {
        $this->printerAdapter = $printer ?? new DefaultPrinterAdapter();
    }

    /**
     * register filter
     *
     * @param OutputFilterInterface $filter
     * @return void
     */
    public function registerFilter(OutputFilterInterface $filter): void
    {
        $this->outputFilters[] = $filter;
    }

    /**
     * clear registered filters
     *
     * @return void
     */
    public function clearFilters(): void
    {
        $this->outputFilters = [];
    }

    /**
     * load application instance
     *
     * @param App $app
     * @return void
     */
    public function load(App $app): void
    {
    }

    /**
     * Pass content through current configured filter(s)
     *
     * @param  string $content
     * @param  string|null $style
     * @return string
     */
    public function filterOutput(string $content, ?string $style = null): string
    {
        foreach ($this->outputFilters as $filter) {
            $content = $filter->filter($content, $style);
        }

        return $content;
    }

    /**
     * Prints a content using configured filters
     *
     * @param string $content
     * @param string $style
     */
    public function out(string $content, string $style = "default"): void
    {
        print $this->printerAdapter->out($this->filterOutput($content, $style));
    }

    /**
     * Prints content without formatting or styling
     *
     * @param string $content
     * @return void
     */
    public function rawOutput(string $content): void
    {
        print $this->printerAdapter->out($content);
    }

    /**
     * prints a new line
     *
     * @return void
     */
    public function newline(): void
    {
        $this->rawOutput("\n");
    }

    /**
     * Displays content using the "default" style
     *
     * @param string $content
     * @param bool $alt Whether or not to use the inverted style ("alt")
     * @return void
     */
    public function display(string $content, bool $alt = false): void
    {
        $this->newline();
        $this->out($content, $alt ? "alt" : "default");
        $this->newline();
    }

    /**
     * Prints content using the "error" style
     *
     * @param string $content
     * @param bool $alt Whether or not to use the inverted style ("error_alt")
     * @return void
     */
    public function error(string $content, bool $alt = false): void
    {
        $this->newline();
        $this->out($content, $alt ? "error_alt" : "error");
        $this->newline();
    }

    /**
     * Prints content using the "info" style
     *
     * @param string $content
     * @param bool $alt Whether or not to use the inverted style ("info_alt")
     * @return void
     */
    public function info(string $content, bool $alt = false): void
    {
        $this->newline();
        $this->out($content, $alt ? "info_alt" : "info");
        $this->newline();
    }

    /**
     * Prints content using the "success" style
     *
     * @param string $content The string to print
     * @param bool $alt Whether or not to use the inverted style ("success_alt")
     * @return void
     */
    public function success(string $content, bool $alt = false): void
    {
        $this->newline();
        $this->out($content, $alt ? "success_alt" : "success");
        $this->newline();
    }

    /**
     * Shortcut method to print tables using the TableHelper
     *
     * @param array $table An array containing all table rows. Each row must be an array with the individual cells.
     */
    public function printTable(array $table): void
    {
        $helper = new TableHelper($table);

        $filter = (isset($this->outputFilters[0]) && $this->outputFilters[0] instanceof OutputFilterInterface) ? $this->outputFilters[0] : null;
        $this->newline();
        $this->rawOutput($helper->getFormattedTable($filter));
        $this->newline();
    }
}
