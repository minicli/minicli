<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Command\CommandController;

class RequiredController extends CommandController
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
