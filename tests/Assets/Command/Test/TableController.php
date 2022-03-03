<?php

namespace Assets\Command\Test;

use Minicli\Command\CommandController;
use Minicli\Output\Helper\TableHelper;

class TableController extends CommandController
{
    public function handle(): void
    {
        $table = new TableHelper();

        $table->addHeader(['ID', 'NAME', 'FIELD3']);

        for ($i = 1; $i <= 10; $i++) {
            $table->addRow([
                $i, 'test', rand(0, 200)
            ]);
        }

        $table->getFormattedTable();
    }
}
