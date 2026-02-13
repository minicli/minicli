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
        $cache = new DiscoveryCache($app);
        $configPath = $app->configPath();

        if (! is_dir($configPath)) {
            return;
        }

        $configFiles = glob($configPath . '/*.php');

        if ($configFiles === false || $configFiles === []) {
            return;
        }

        foreach ($configFiles as $configFile) {
            $configEntries = $cache->rememberFile(
                domain: 'config',
                filePath: $configFile,
                resolver: fn (string $realPath): array => $this->extractConfigEntries($realPath),
            );

            foreach ($configEntries as $configEntry) {
                $configClass = $configEntry['class'];
                $configName = $configEntry['name'];

                if (! class_exists($configClass)) {
                    require_once $configEntry['file'];
                }

                $configInstance = new $configClass();
                $app->addConfig($configName, $configInstance);
            }
        }
    }

    /**
     * @return array<array{class: class-string, name: string, file: string}>
     */
    private function extractConfigEntries(string $filePath): array
    {
        $entries = [];

        foreach ($this->classesDefinedInFile($filePath) as $configClass) {
            $reflectionClass = new ReflectionClass($configClass);
            $configAttributes = $reflectionClass->getAttributes(Config::class);

            if ($configAttributes === []) {
                continue;
            }

            $configAttribute = $configAttributes[0]->newInstance();
            $entries[] = [
                'class' => $configClass,
                'name' => $configAttribute->name,
                'file' => $filePath,
            ];
        }

        return $entries;
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
