<?php

declare(strict_types=1);

namespace Minicli\Output;

use InvalidArgumentException;
use Minicli\App;
use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\PrinterAdapterInterface;
use Minicli\Contracts\ServiceInterface;
use Minicli\Input\Input;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Output\Helper\TableHelper;

final class OutputHandler implements ServiceInterface
{
    /**
     * @param  array<int, OutputFilterInterface>  $outputFilters
     */
    public function __construct(
        private readonly PrinterAdapterInterface $printerAdapter = new DefaultPrinterAdapter(),
        private array $outputFilters = [],
    ) {}

    public function registerFilter(OutputFilterInterface $filter): void
    {
        $this->outputFilters[] = $filter;
    }

    public function clearFilters(): void
    {
        $this->outputFilters = [];
    }

    public function load(App $app): void {}

    public function filterOutput(string $content, ?string $style = null): string
    {
        foreach ($this->outputFilters as $filter) {
            $content = $filter->filter($content, $style);
        }

        return $content;
    }

    public function out(string $content, string $style = 'default'): void
    {
        echo $this->printerAdapter->out($this->filterOutput($content, $style));
    }

    public function rawOutput(string $content): void
    {
        echo $this->printerAdapter->out($content);
    }

    public function newline(): void
    {
        $this->rawOutput("\n");
    }

    public function divider(int $length = 10): void
    {
        $this->rawOutput(str_repeat('-', $length));
    }

    public function breathe(string $content, string $style): void
    {
        $this->newline();
        $this->out($content, $style);
        $this->newline();
    }

    public function display(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'alt' : 'default');
    }

    public function error(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'error_alt' : 'error');
    }

    public function info(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'info_alt' : 'info');
    }

    public function success(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? 'success_alt' : 'success');
    }

    /**
     * @param  array<int, array<string>>  $table
     */
    public function printTable(array $table): void
    {
        $helper = new TableHelper($table);

        $filter = $this->outputFilters[0] ?? null;
        $this->newline();
        $this->rawOutput($helper->getFormattedTable($filter));
        $this->newline();
    }

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
