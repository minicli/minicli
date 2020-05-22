<?php

namespace Assets\Command\Test;

use Minicli\App;
use Minicli\Command\CommandController;
use Minicli\Output\Helper\TableHelper;

class TableController extends CommandController
{

    public function handle()
    {
        $this->getPrinter();

        $table = new TableHelper();
    }
}