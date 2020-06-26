<?php

declare(strict_types = 1);

namespace Minicli\Command;

use Minicli\ControllerInterface;

class ParsedCommand
{
    /** @var CommandCall */
    private $commandCall;

    /** @var callable */
    private $callable;

    /** @var ControllerInterface|null */
    private $controller;

    /**
     *
     * @param CommandCall $commandCall
     * @param callable $callable
     */
    public function __construct(
        CommandCall $commandCall,
        callable $callable,
        ?ControllerInterface $controller
    ) {
        $this->commandCall = $commandCall;
        $this->callable = $callable;
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->commandCall->getParams();
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->commandCall->getFlags();
    }

    /**
     * Get the callable that is meant to be called for this command
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}
