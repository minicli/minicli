<?php

declare(strict_types=1);

namespace Minicli;

interface ServiceInterface
{
    /**
     * load application
     */
    public function load(App $app): void;
}
