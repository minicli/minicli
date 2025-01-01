<?php

declare(strict_types=1);

namespace Minicli\PrebuiltCommands;

use Minicli\App;

class PrebuiltCommander
{
    public readonly HelpCommand $help;

    public function __construct(App $app)
    {
        $this->help = new HelpCommand($app);
    }
}
