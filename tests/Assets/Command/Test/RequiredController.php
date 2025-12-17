<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\ConsoleCommand;

class RequiredController extends ConsoleCommand
{
    public function handle(): void
    {
        $this->rawOutput("Hello, {$this->getParam('name')}");
    }

    public function required(): array
    {
        return ['name'];
    }
}
