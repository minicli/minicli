<?php

declare(strict_types=1);

namespace Minicli\Support;

use Minicli\App;
use Minicli\Attributes\Config;
use Minicli\Exceptions\BindingResolutionException;
use ReflectionClass;
use ReflectionException;

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
            $configClasses = $this->classesDefinedInFile($configFile);

            foreach ($configClasses as $configClass) {
                $reflectionClass = new ReflectionClass($configClass);
                $configAttributes = $reflectionClass->getAttributes(Config::class);

                if ($configAttributes === []) {
                    continue;
                }

                $configAttribute = $configAttributes[0]->newInstance();
                $configName = $configAttribute->name;

                $configInstance = new $configClass();
                $app->addConfig($configName, $configInstance);
            }
        }
    }

    /**
     * @return array<class-string>
     */
    private function classesDefinedInFile(string $filePath): array
    {
        $realPath = realpath($filePath);
        if ($realPath === false) {
            return [];
        }

        require_once $realPath;

        $classes = [];

        foreach (get_declared_classes() as $className) {
            if (! class_exists($className)) {
                continue;
            }

            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            if ($reflection->getFileName() === $realPath) {
                $classes[] = $className;
            }
        }

        return $classes;
    }
}
