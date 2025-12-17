<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\ConsoleCommand;
use Minicli\Output\Table\TableBuilder;

class TableController extends ConsoleCommand
{
    public function handle(): void
    {
        $table = new TableBuilder();

        $table->addHeader(['ID', 'NAME', 'FIELD3']);

        for ($i = 1; $i <= 10; $i++) {
            $table->addRow([
                $i, 'test', random_int(0, 200),
            ]);
        }

        $table->table();
    }
}
