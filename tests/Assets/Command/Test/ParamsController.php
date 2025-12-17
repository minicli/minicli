<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\ConsoleCommand;

class ParamsController extends ConsoleCommand
{
    public function handle(): void
    {
        $print = count($this->getArgs());

        if ($this->hasFlag('--count-params')) {
            $print = count($this->getParams());
        }

        $this->rawOutput((string) $print);
    }
}
