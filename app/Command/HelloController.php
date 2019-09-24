<?php

namespace App\Command;

use Minicli\CommandController;

class HelloController extends CommandController
{
    public function run($argv)
    {
        $name = isset ($argv[2]) ? $argv[2] : "World";
        $this->getApp()->getPrinter()->display("Hello $name!!!");
    }
}