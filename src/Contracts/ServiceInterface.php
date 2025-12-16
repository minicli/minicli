<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\App;

interface ServiceInterface
{
    /**
     * Runs when adding the service to the container
     */
    public function load(App $app): void;
}
