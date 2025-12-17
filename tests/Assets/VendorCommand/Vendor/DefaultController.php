<?php

declare(strict_types=1);

namespace Assets\VendorCommand\Vendor;

use Minicli\Console\ConsoleCommand;

class DefaultController extends ConsoleCommand
{
    public function handle(): void
    {
        $this->rawOutput('test vendor');
    }
}
