<?php

declare(strict_types=1);

namespace Minicli\Output;

use Minicli\App;
use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\PrinterAdapterInterface;
use Minicli\Contracts\ServiceInterface;
use Minicli\Input\Input;
use Minicli\Output\Adapter\DefaultPrinterAdapter;
use Minicli\Output\Components\Table\TableBuilder;
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

    /**
     * @param  array<StyleType>  $formats
     */
    public function filterOutput(string $content, ?StyleType $style = null, array $formats = []): string
    {
        foreach ($this->outputFilters as $filter) {
            $content = $filter->filter($content, $style, $formats);
        }

        return $content;
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function out(string $content, StyleType $style = StyleType::DEFAULT, array $formats = []): void
    {
        echo $this->printerAdapter->out($this->filterOutput($content, $style, $formats));
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

    /**
     * @param  array<StyleType>  $formats
     */
    public function breathe(string $content, StyleType $style, array $formats = []): void
    {
        $this->newline();
        $this->out($content, $style, $formats);
        $this->newline();
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function display(string $content, bool $alt = false, array $formats = []): void
    {
        $this->breathe($content, $alt ? StyleType::ALT : StyleType::DEFAULT, $formats);
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function error(string $content, bool $alt = false, array $formats = []): void
    {
        $this->breathe($content, $alt ? StyleType::ERROR_ALT : StyleType::ERROR, $formats);
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function warning(string $content, bool $alt = false, array $formats = []): void
    {
        $this->breathe($content, $alt ? StyleType::WARNING_ALT : StyleType::WARNING, $formats);
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function info(string $content, bool $alt = false, array $formats = []): void
    {
        $this->breathe($content, $alt ? StyleType::INFO_ALT : StyleType::INFO, $formats);
    }

    /**
     * @param  array<StyleType>  $formats
     */
    public function success(string $content, bool $alt = false, array $formats = []): void
    {
        $this->breathe($content, $alt ? StyleType::SUCCESS_ALT : StyleType::SUCCESS, $formats);
    }

    public function table(TableBuilder $builder): void
    {
        $filter = $this->outputFilters[0] ?? null;
        $this->newline();
        $this->rawOutput($builder->table($filter));
        $this->newline();
    }

    public function ask(string $content = '', bool $required = true): string
    {
        if ($content !== '') {
            $this->display($content);
        }

        $input = new Input()->read();
        if ($input === '' && $required) {
            $this->warning('Input cannot be empty. Please provide a value.');

            return $this->ask($content, $required);
        }

        return $input;
    }
}
