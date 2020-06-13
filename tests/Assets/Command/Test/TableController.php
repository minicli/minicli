<?php

namespace Assets\Command\Test;

use Minicli\App;
use Minicli\Command\CommandController;
use Minicli\Output\Helper\TableHelper;

class TableController extends CommandController
{
    public function handle()
    {
        $table = new TableHelper();

        $table->addHeader(['ID', 'NAME', 'FIELD3']);

        for ($i = 1; $i <= 10; $i++) {
            $table->addRow([
                $i, 'test', rand(0, 200)
            ]);
        }

        return $table->getFormattedTable();
    }
}
