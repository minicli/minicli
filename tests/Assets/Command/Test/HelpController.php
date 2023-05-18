<?php

declare(strict_types=1);

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

        $this->rawOutput($shout ? mb_strtoupper("Hello {$name}") : "Hello {$name}");
    }
}
