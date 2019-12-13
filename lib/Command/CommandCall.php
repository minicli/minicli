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

    /** @var array */
    public $params = [];

    /**
     * CommandCall constructor.
     * @param array $argv
     */
    public function __construct(array $argv)
    {
        $this->args = $argv;
        $this->command = isset($argv[1]) ? $argv[1] : null;
        $this->subcommand = isset($argv[2]) ? $argv[2] : 'default';

        $this->loadParams($argv);
    }

    /**
     * @param array $args
     */
    protected function loadParams(array $args)
    {
        foreach ($args as $arg) {
            $pair = explode('=', $arg);
            if (count($pair) == 2) {
                $this->params[$pair[0]] = $pair[1];
            }
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

    public function hasFlag($flag)
    {
        return in_array($flag, $this->args);
    }

    /**
     * @param string $param
     * @return string|null
     */
    public function getParam($param)
    {
        return $this->hasParam($param) ? $this->params[$param] : null;
    }
}