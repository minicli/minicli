<?php

declare(strict_types=1);

namespace Minicli\Command;

class CommandCall
{
    /**
     * command
     */
    public ?string $command;

    /**
     * sub command
     */
    public string $subcommand;

    /**
     * arguments
     *
     * @var array<int, string>
     */
    public array $args = [];

    /**
     * parameters
     *
     * @var array<string, string>
     */
    public array $params = [];

    /**
     * flags
     *
     * @var array<int|string, string>
     */
    public array $flags = [];

    /**
     * CommandCall constructor.
     *
     * @param array<int, string> $rawArgs
     */
    public function __construct(/**
     * raw arguments
     */
    public array $rawArgs)
    {
        $this->parseCommand($this->rawArgs);

        $this->command = $this->args[1] ?? null;

        $this->subcommand = $this->args[2] ?? 'default';
    }

    /**
     * check has parameter
     */
    public function hasParam(string $param): bool
    {
        return isset($this->params[$param]);
    }

    /**
     * check has flag
     */
    public function hasFlag(string $flag): bool
    {
        if (in_array($flag, $this->flags)) {
            return true;
        }

        return in_array('--' . $flag, $this->flags);
    }

    /**
     * get parameter
     */
    public function getParam(string $param): ?string
    {
        return $this->hasParam($param) ? $this->params[$param] : null;
    }

    /**
     * get raw args
     *
     * @return array<int, string>
     */
    public function getRawArgs(): array
    {
        return $this->rawArgs;
    }

    /**
     * get flags
     *
     * @return array<int|string, string>
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * parse command
     *
     * @param  array<int, string>  $argv
     */
    protected function parseCommand(array $argv): void
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
