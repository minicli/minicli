<?php

declare(strict_types=1);

namespace Assets\Command\Test;

use Minicli\Console\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->rawOutput('test default');
    }
}
