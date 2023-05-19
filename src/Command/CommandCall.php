<?php

declare(strict_types=1);

namespace Minicli\Command;

class CommandCall
{
    /**
     * command
     *
     * @var string|null $command
     */
    public ?string $command;

    /**
     * sub command
     *
     * @var string $subcommand
     */
    public string $subcommand;

    /**
     * arguments
     *
     * @var array<int, string> $args
     */
    public array $args = [];

    /**
     * raw arguments
     *
     * @var array<int, string> $rawArgs
     */
    public array $rawArgs = [];

    /**
     * parameters
     *
     * @var array<string, string> $params
     */
    public array $params = [];

    /**
     * flags
     *
     * @var array<int|string, string> $flags
     */
    public array $flags = [];

    /**
     * CommandCall constructor.
     *
     * @param array<int, string> $argv
     */
    public function __construct(array $argv)
    {
        $this->rawArgs = $argv;

        $this->parseCommand($argv);

        $this->command = $this->args[1] ?? null;

        $this->subcommand = $this->args[2] ?? 'default';
    }

    /**
     * parse command
     *
     * @param array<int, string> $argv
     * @return void
     */
    protected function parseCommand(array $argv): void
    {
        foreach ($argv as $arg) {
            $parts = explode('=', $arg);

            if (count($parts) >= 2) {
                $this->params[$parts[0]] = join('=', array_slice($parts, 1));
                continue;
            }

            if (str_starts_with($arg, '--')) {
                $this->flags[] = $arg;
                continue;
            }

            $this->args[] = $arg;
        }
    }

    /**
     * check has parameter
     *
     * @param string $param
     * @return bool
     */
    public function hasParam(string $param): bool
    {
        return isset($this->params[$param]);
    }

    /**
     * check has flag
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        if (in_array($flag, $this->flags)) {
            return true;
        }

        return in_array('--'.$flag, $this->flags);
    }

    /**
     * get parameter
     *
     * @param string $param
     * @return string|null
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
}
