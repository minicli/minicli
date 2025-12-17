<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\App;

interface CommandInterface
{
    /**
     * Called after the command execution
     */
    public function teardown(): void;

    /**
     * Called before the command execution
     */
    public function boot(App $app): void;
}
