<?php

declare(strict_types=1);

namespace Minicli\Support;

use Minicli\App;
use Minicli\Attributes\Service;
use Minicli\Contracts\ServiceInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Log\Logger;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

final readonly class ServiceLoader
{
    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function load(App $app): void
    {
        $cache = new DiscoveryCache($app);
        $this->loadDefaultServices($app);
        $servicesPath = $app->basePath() . '/app/Services';

        if (! is_dir($servicesPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($servicesPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getRealPath();
            if (! is_string($filePath)) {
                continue;
            }

            $serviceEntries = $cache->rememberFile(
                domain: 'services',
                filePath: $filePath,
                resolver: fn (string $realPath): array => $this->extractServiceEntries($realPath),
            );

            foreach ($serviceEntries as $serviceEntry) {
                $className = $serviceEntry['class'];
                $serviceName = $serviceEntry['name'];

                if (! class_exists($className)) {
                    require_once $serviceEntry['file'];
                }

                /** @var ServiceInterface $serviceInstance */
                $serviceInstance = $app->make($className);
                $app->addService($serviceName, $serviceInstance);
            }
        }
    }

    /**
     * @return array<array{class: class-string, name: string, file: string}>
     */
    private function extractServiceEntries(string $filePath): array
    {
        $entries = [];

        foreach ($this->classesDefinedInFile($filePath) as $className) {
            $reflectionClass = new ReflectionClass($className);

            $serviceAttributes = $reflectionClass->getAttributes(Service::class);

            if ($serviceAttributes === []) {
                continue;
            }

            if (! $reflectionClass->implementsInterface(ServiceInterface::class)) {
                continue;
            }

            $serviceAttribute = $serviceAttributes[0]->newInstance();
            $entries[] = [
                'class' => $className,
                'name' => $serviceAttribute->name,
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

    private function loadDefaultServices(App $app): void
    {
        $app->addService('logger', new Logger());
    }
}
