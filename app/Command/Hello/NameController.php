<?php

namespace App\Command\Hello;

use Minicli\Command\CommandController;

class NameController extends CommandController
{
    public function handle()
    {
        $name = $this->hasParam('user') ? $this->getParam('user') : 'World';

        $this->getPrinter()->display(sprintf("Hello, %s!", $name));
    }
}