<?php

declare(strict_types=1);

namespace Minicli\Console;

final class CommandCall
{
    public string $command;

    public ?string $subcommand;

    /**
     * @var array<int, string>
     */
    public array $args = [];

    /**
     * @var array<string, string>
     */
    public array $params = [];

    /**
     * @var array<int|string, string>
     */
    public array $flags = [];

    /**
     * @param  array<int, string>  $rawArgs
     */
    public function __construct(public array $rawArgs)
    {
        $this->parseCommand($this->rawArgs);
        $this->command = $this->args[1] ?? '';
        $this->subcommand = $this->args[2] ?? null;
    }

    public function hasParam(string $param): bool
    {
        return isset($this->params[$param]);
    }

    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags) || in_array("--{$flag}", $this->flags);
    }

    public function getParam(string $param): ?string
    {
        return $this->hasParam($param) ? $this->params[$param] : null;
    }

    /**
     * @return array<int, string>
     */
    public function getRawArgs(): array
    {
        return $this->rawArgs;
    }

    /**
     * @return array<int|string, string>
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @param  array<int, string>  $argv
     */
    private function parseCommand(array $argv): void
    {
        foreach ($argv as $arg) {
            $parts = explode('=', $arg);

            if (count($parts) >= 2) {
                $this->params[$parts[0]] = implode('=', array_slice($parts, 1));

                continue;
            }

            if (str_starts_with($arg, '--')) {
                $this->flags[] = $arg;

                continue;
            }

            $this->args[] = $arg;
        }
    }
}
