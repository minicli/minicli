<?php

declare(strict_types=1);

namespace Minicli\Console;

use Closure;
use Minicli\Components\Alert;
use Minicli\Components\Divider;
use Minicli\Components\LineBreak;
use Minicli\Components\Table\Row;
use Minicli\Components\Table\Table;
use Minicli\Components\Text;

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

    public function displayHelp(): ExitCode
    {
        if ($this->description !== '') {
            Text::make($this->description)->info()->bold()->render();
        }

        if ($this->arguments === []) {
            Alert::make('This command has no arguments.')->warning()->render();
        } else {
            Text::make('Arguments')->info()->bold()->render();

            $table = Table::make()->withBorders();
            $table->addRow(Row::make(['ARGUMENT', 'DESCRIPTION', 'REQUIRED', 'DEFAULT'])->bold()->info());
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

            LineBreak::make()->render();
            $table->render();
        }

        Divider::make()->fullWidth()->render();
        Text::make('Global Flags')->info()->bold()->render();

        $flagsTable = Table::make()->withBorders();
        $flagsTable->addRow(Row::make(['FLAG', 'DESCRIPTION'])->bold()->info());

        foreach (GlobalFlag::cases() as $flag) {
            $flagsTable->addRow(Row::make([$flag->value, $flag->description()]));
        }

        LineBreak::make()->render();
        $flagsTable->render();

        return ExitCode::Success;
    }
}
