<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Factories\AppFactory;

test('it can create a new app')
    ->expect(fn () => AppFactory::make(
        config: [
            'app_path' => __DIR__ . '/../app/Command',
            'theme' => '',
            'debug' => true,
        ],
        signature: '/minicli help',
    ))->toBeInstanceOf(App::class);
