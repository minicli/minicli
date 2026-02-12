<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Services;

use Minicli\App;
use Minicli\Attributes\Service;
use Minicli\Contracts\ServiceInterface;

#[Service('test')]
final class TestService implements ServiceInterface
{
    public function load(App $app): void {}

    public function hello(): string
    {
        return 'Hello World!';
    }
}
