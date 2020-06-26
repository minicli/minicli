<?php

declare(strict_types = 1);


namespace Minicli;

use Minicli\Command\ParsedCommand;

interface Invoker
{
    public function invokeParsedCommand(ParsedCommand $parsedCommand);
}