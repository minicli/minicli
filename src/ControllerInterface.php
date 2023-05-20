<?php

declare(strict_types=1);

namespace Minicli;

use Minicli\Command\CommandCall;
use Minicli\Exception\MissingParametersException;

interface ControllerInterface
{
    /**
     * Called before `run`
     *
     * @param App $app
     * @param CommandCall $input
     * @return void
     * @throws MissingParametersException
     */
    public function boot(App $app, CommandCall $input): void;

    /**
     * Main execution
     *
     * @param CommandCall $input
     * @return void
     */
    public function run(CommandCall $input): void;

    /**
     * The list of parameters required by the command.
     *
     * @return array<int,string>
     */
    public function required(): array;

    /**
     * Called when `run` is successfully finished
     *
     * @return void
     */
    public function teardown(): void;
}
