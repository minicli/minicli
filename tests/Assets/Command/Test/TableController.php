<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\ConsoleCommand;
use Minicli\Output\Helper\TableHelper;

class TableController extends ConsoleCommand
{
    public function handle(): void
    {
        $table = new TableHelper();

        $table->addHeader(['ID', 'NAME', 'FIELD3']);

        for ($i = 1; $i <= 10; $i++) {
            $table->addRow([
                $i, 'test', random_int(0, 200),
            ]);
        }

        $table->getFormattedTable();
    }
}
