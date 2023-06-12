<?php

declare(strict_types=1);

namespace Assets\Services;

use Minicli\App;
use Minicli\ServiceInterface;

class TestService implements ServiceInterface
{
    public function load(App $app): void
    {
    }

    public function hello(): string
    {
        return 'Hello World!';
    }
}
