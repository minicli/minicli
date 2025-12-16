<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Exceptions\MissingParametersException;

interface ControllerInterface
{
    /**
     * Called when `run` is successfully finished
     */
    public function teardown(): void;

    /**
     * Called before `run`
     *
     * @throws MissingParametersException
     */
    public function boot(App $app, CommandCall $input): void;

    /**
     * Main execution
     */
    public function run(CommandCall $input): void;

    /**
     * The list of parameters required by the command.
     *
     * @return array<int,string>
     */
    public function required(): array;
}
