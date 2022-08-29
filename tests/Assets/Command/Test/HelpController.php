<?php

namespace Assets\Command\Test;

use Minicli\Command\CommandController;

class HelpController extends CommandController
{
    public function handle(): void
    {
        $name = "default";

        //test for arguments
        if ($this->hasParam('name')) {
            $name = $this->getParam('name');
        }

        //test for flags
        $shout = false;

        if ($this->hasFlag('--shout')) {
            $shout = true;
        }

        $this->getPrinter()->rawOutput($shout ? strtoupper("Hello $name") : "Hello $name");
    }
}
