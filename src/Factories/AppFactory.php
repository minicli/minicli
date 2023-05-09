<?php

declare(strict_types=1);

namespace Minicli\Factories;

use Minicli\App;

final class AppFactory
{
    /**
     * Create a new Instance of an App.
     * @param array $config
     * @param string $signature
     * @return App
     */
    public static function make(
        array $config = [],
        string $signature = '/minicli help',
    ): App {
        return new App(
            config: $config,
            signature: $signature,
        );
    }
}
