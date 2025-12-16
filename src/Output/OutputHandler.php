<?php

declare(strict_types=1);

namespace Minicli\Output;

use InvalidArgumentException;
use Minicli\App;
use Minicli\Input;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Output\Helper\TableHelper;
use Minicli\ServiceInterface;

class OutputHandler implements ServiceInterface
{
    /**
     * @param  array<int, OutputFilterInterface>  $outputFilters
     */
    public function __construct(
        protected PrinterAdapterInterface $printerAdapter = new DefaultPrinterAdapter(),
        protected array $outputFilters = [],
    ) {}

    /**
     * register filter
     */
    public function registerFilter(OutputFilterInterface $filter): void
    {
        $this->outputFilters[] = $filter;
    }

    /**
     * clear registered filters
     */
    public function clearFilters(): void
    {
        $this->outputFilters = [];
    }

    /**
     * load application instance
     */
    public function load(App $app): void {}

    /**
     * Pass content through current configured filter(s)
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
     */
    public function out(string $content, string $style = 'default'): void
    {
        echo $this->printerAdapter->out($this->filterOutput($content, $style));
    }

    /**
     * Prints content without formatting or styling
     */
    public function rawOutput(string $content): void
    {
        echo $this->printerAdapter->out($content);
    }

    /**
     * prints a new line
     */
    public function newline(): void
    {
        $this->rawOutput("\n");
    }

    /**
     * Print the output with a newline either side.
     */
    public function breathe(string $content, string $style): void
    {
        $this->newline();
        $this->out($content, $style);
        $this->newline();
    }

    /**
     * Displays content using the "default" style
     *
     * @param  bool  $alt  Use the inverted style ("alt")
     */
    public function display(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'alt' : 'default');
    }

    /**
     * Prints content using the "error" style
     *
     * @param  bool  $alt  Use the inverted style ("error_alt")
     */
    public function error(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'error_alt' : 'error');
    }

    /**
     * Prints content using the "info" style
     *
     * @param  bool  $alt  Use the inverted style ("info_alt")
     */
    public function info(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'info_alt' : 'info');
    }

    /**
     * Prints content using the "success" style
     *
     * @param  string  $content  The string to print
     * @param  bool  $alt  Use the inverted style ("success_alt")
     */
    public function success(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'success_alt' : 'success');
    }

    /**
     * Shortcut method to print tables using the TableHelper
     *
     * @param  array<int, array<string>>  $table  An array containing all table rows. Each row must be an array with the individual cells.
     */
    public function printTable(array $table): void
    {
        $helper = new TableHelper($table);

        $filter = $this->outputFilters[0] ?? null;
        $this->newline();
        $this->rawOutput($helper->getFormattedTable($filter));
        $this->newline();
    }

    /**
     * Ask the users input
     */
    public function ask(string $content, string $method = 'display'): string
    {
        if (! method_exists($this, $method)) {
            throw new InvalidArgumentException(
                message: "No output for [{$method}]",
            );
        }

        $this->{$method}(
            $content,
        );

        return new Input()->read();
    }
}
