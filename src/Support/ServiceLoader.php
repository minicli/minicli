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
        $this->loadDefaultServices($app);

        $basePath = $app->basePath();

        if (! is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $processedClasses = [];

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $filePath = $file->getRealPath();
            // Skip vendor and config directories
            if (str_contains((string) $filePath, '/vendor/')) {
                continue;
            }
            if (str_contains((string) $filePath, '/config/')) {
                continue;
            }

            // Track classes before requiring file
            $classesBefore = get_declared_classes();

            require_once $filePath;

            // Get newly declared classes from this file
            $classesAfter = get_declared_classes();
            $newClasses = array_diff($classesAfter, $classesBefore);

            foreach ($newClasses as $className) {
                // Skip if already processed
                if (isset($processedClasses[$className])) {
                    continue;
                }

                $processedClasses[$className] = true;

                if (! class_exists($className)) {
                    continue;
                }

                $reflectionClass = new ReflectionClass($className);

                // Check if class has Service attribute
                $serviceAttributes = $reflectionClass->getAttributes(Service::class);

                if ($serviceAttributes === []) {
                    continue;
                }

                // Check if class implements ServiceInterface
                if (! $reflectionClass->implementsInterface(ServiceInterface::class)) {
                    continue;
                }

                $serviceAttribute = $serviceAttributes[0]->newInstance();
                $serviceName = $serviceAttribute->name;

                /** @var ServiceInterface $serviceInstance */
                $serviceInstance = new $className();
                $app->addService($serviceName, $serviceInstance);
            }
        }
    }

    private function loadDefaultServices(App $app): void
    {
        $app->addService('logger', new Logger());
    }
}
