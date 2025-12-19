<?php

declare(strict_types=1);

namespace Minicli\Output;

use Minicli\App;
use Minicli\Components\Table\TableBuilder;
use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\ServiceInterface;
use Minicli\Input\Input;

final class OutputHandler implements ServiceInterface
{
    /**
     * @param  array<OutputFilterInterface>  $outputFilters
     */
    public function __construct(
        private array $outputFilters = [],
    ) {}

    public function load(App $app): void {}

    public function divider(int $length = 10): void
    {
        $this->rawOutput(str_repeat('-', $length));
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
