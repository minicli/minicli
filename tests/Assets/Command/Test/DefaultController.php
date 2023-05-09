<?php

namespace Assets\Command\Test;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->getPrinter()->rawOutput('test default');
    }
}
