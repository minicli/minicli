<?php

declare(strict_types=1);

namespace Minicli\Support;

use Minicli\App;
use Minicli\Attributes\Config;
use Minicli\Exceptions\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

final readonly class ConfigLoader
{
    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function load(App $app): void
    {
        $configPath = $app->configPath();

        if (! is_dir($configPath)) {
            return;
        }

        $configFiles = glob($configPath . '/*.php');

        if ($configFiles === false || $configFiles === []) {
            return;
        }

        foreach ($configFiles as $configFile) {
            require_once $configFile;

            $className = basename($configFile, '.php');

            if (! class_exists($className)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);
            $configAttributes = $reflectionClass->getAttributes(Config::class);

            if ($configAttributes === []) {
                throw new RuntimeException("Configuration class {$className} must have a Config attribute.");
            }

            $configAttribute = $configAttributes[0]->newInstance();
            $configName = $configAttribute->name;

            $configInstance = new $className();
            $app->addConfig($configName, $configInstance);
        }
    }
}
