<?php

declare(strict_types=1);

namespace Minicli;

use Minicli\Command\CommandCall;

interface ControllerInterface
{
    /**
     * Called before `run`
     *
     * @param App $app
     * @return void
     */
    public function boot(App $app): void;

    /**
     * Main execution
     *
     * @param CommandCall $input
     * @return void
     */
    public function run(CommandCall $input): void;

    /**
     * Called when `run` is successfully finished
     *
     * @return void
     */
    public function teardown(): void;
}
