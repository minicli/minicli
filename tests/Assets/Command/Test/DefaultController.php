<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\ConsoleCommand;

class DefaultController extends ConsoleCommand
{
    public function handle(): void
    {
        $this->rawOutput('test default');
    }
}
