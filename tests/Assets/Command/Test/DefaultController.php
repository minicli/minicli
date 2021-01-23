<?php

namespace Assets\Command\Test;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle()
    {
        $this->getPrinter()->rawOutput('test default');
    }
}
