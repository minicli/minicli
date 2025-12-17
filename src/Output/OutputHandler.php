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
use Minicli\Output\Table\TableBuilder;
use Minicli\Output\Theming\StyleType;

final class OutputHandler implements ServiceInterface
{
    /**
     * @param  array<OutputFilterInterface>  $outputFilters
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

    public function filterOutput(string $content, ?StyleType $style = null): string
    {
        foreach ($this->outputFilters as $filter) {
            $content = $filter->filter($content, $style);
        }

        return $content;
    }

    public function out(string $content, StyleType $style = StyleType::DEFAULT): void
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

    public function breathe(string $content, StyleType $style): void
    {
        $this->newline();
        $this->out($content, $style);
        $this->newline();
    }

    public function display(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? StyleType::ALT : StyleType::DEFAULT);
    }

    public function error(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? StyleType::ERROR_ALT : StyleType::ERROR);
    }

    public function warning(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? StyleType::WARNING_ALT : StyleType::WARNING);
    }

    public function info(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? StyleType::INFO_ALT : StyleType::INFO);
    }

    public function success(string $content, bool $alt = false): void
    {
        $this->breathe($content, $alt ? StyleType::SUCCESS_ALT : StyleType::SUCCESS);
    }

    public function table(TableBuilder $builder): void
    {
        $filter = $this->outputFilters[0] ?? null;
        $this->newline();
        $this->rawOutput($builder->table($filter));
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
