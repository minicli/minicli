<?php

namespace Assets\VendorCommand\Vendor;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->rawOutput('test vendor');
    }
}
