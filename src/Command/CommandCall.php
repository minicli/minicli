<?php

declare(strict_types=1);

namespace Minicli\Command;

class CommandCall
{
    /**
     * command
     *
     * @param string|null $command
     */
    public ?string $command;

    /**
    * sub command
    *
    * @param string $subcommand
    */
    public string $subcommand;

    /**
     * arguments
     *
     * @param array $args
     */
    public array $args = [];

    /**
    * raw arguments
    *
    * @param array $rawArgs
    */
    public array $rawArgs = [];

    /**
     * parameters
     *
     * @param array $params
     */
    public array $params = [];

    /**
     * flags
     *
     * @param array $flags
     */
    public array $flags = [];

    /**
     * CommandCall constructor.
     *
     * @param array $argv
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
     * @param array $argv
     * @return void
     */
    protected function parseCommand(array $argv): void
    {
        foreach ($argv as $arg) {
            $pair = explode('=', $arg);

            if (count($pair) == 2) {
                $this->params[$pair[0]] = $pair[1];
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

        return in_array('--' . $flag, $this->flags);
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
     * @return array
     */
    public function getRawArgs(): array
    {
        return $this->rawArgs;
    }

    /**
     * get flags
     *
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }
}
