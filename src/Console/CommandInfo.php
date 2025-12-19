<?php

declare(strict_types=1);

namespace Minicli\Console;

use Closure;
use Minicli\App;
use Minicli\Components\Table\Row;
use Minicli\Components\Table\TableBuilder;
use Minicli\Components\Text;
use Minicli\Output\Theming\StyleType;

final readonly class CommandInfo
{
    public function __construct(
        public Closure $callable,
        public string $name,
        public string $description = '',
        public ?CommandInfo $parent = null,
        /** @var array<ArgumentInfo> $arguments */
        public array $arguments = [],
    ) {}

    public function displayHelp(App $app): ExitCode
    {
        if ($this->description !== '') {
            Text::make($this->description)->info()->bold()->render();
        }

        if ($this->arguments === []) {
            Text::make('This command has no arguments.')->warning()->bold()->render();

            return ExitCode::Success;
        }

        $table = TableBuilder::make();
        $table->addRow(Row::make(['ARGUMENT', 'DESCRIPTION', 'REQUIRED', 'DEFAULT'], StyleType::ALT));

        foreach ($this->arguments as $argumentInfo) {
            /** @var string $defaultValue */
            $defaultValue = match (true) {
                $argumentInfo->required => 'N/A',
                $argumentInfo->default === null => 'NULL',
                is_bool($argumentInfo->default) => $argumentInfo->default ? 'TRUE' : 'FALSE',
                is_array($argumentInfo->default) => json_encode($argumentInfo->default),
                default => (string) $argumentInfo->default,
            };

            $table->addRow(Row::make([
                $argumentInfo->name,
                $argumentInfo->description,
                $argumentInfo->required ? 'YES' : 'NO',
                $defaultValue,
            ]));
        }

        $app->table($table);

        return ExitCode::Success;
    }
}
