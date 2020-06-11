<?php

namespace Minicli\Command;

class CommandCall
{
    /** @var string  */
    public $command;

    /** @var string */
    public $subcommand;

    /** @var array */
    public $args = [];

    /** @var array  */
    public $raw_args = [];

    /** @var array */
    public $params = [];

    /** @var array  */
    public $flags = [];

    /**
     * CommandCall constructor.
     * @param array $argv
     */
    public function __construct(array $argv)
    {
        $this->raw_args = $argv;
        $this->parseCommand($argv);

        $this->command = isset($this->args[1]) ? $this->args[1] : null;

        $this->subcommand = isset($this->args[2]) ? $this->args[2] : 'default';
    }

    protected function parseCommand($argv)
    {
        foreach ($argv as $arg) {
            $pair = explode('=', $arg);

            if (count($pair) == 2) {
                $this->params[$pair[0]] = $pair[1];
                continue;
            }

            if (substr($arg, 0, 2) == '--') {
                $this->flags[] = $arg;
                continue;
            }

            $this->args[] = $arg;
        }
    }

    /**
     * @param string $param
     * @return bool
     */
    public function hasParam($param)
    {
        return isset($this->params[$param]);
    }

    /**
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return in_array($flag, $this->flags);
    }

    /**
     * @param string $param
     * @return string|null
     */
    public function getParam($param)
    {
        return $this->hasParam($param) ? $this->params[$param] : null;
    }

    /**
     * @return array
     */
    public function getRawArgs()
    {
        return $this->raw_args;
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
