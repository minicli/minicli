<?php

declare(strict_types=1);

namespace Minicli;

/**
 * @property string $app_name
 * @property string|array $app_path
 * @property string $theme
 * @property array<string, class-string<ServiceInterface>> $services
 * @property array<string, string> $logging
 * @property boolean $debug
 */
class Config implements ServiceInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        protected array $config = [],
    ) {
    }

    /**
     * Get configuration value
     *
     * @param string $name Configuration key
     * @return mixed Configuration value (typically string, array, bool, int) or null if not found
     */
    public function __get(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    /**
     * Set configuration value
     *
     * @param string $name Configuration key
     * @param mixed $value Configuration value (typically string, array, bool, int)
     */
    public function __set(string $name, mixed $value): void
    {
        $this->config[$name] = $value;
    }

    /**
     * check if has config
     *
     * @param  string $name
     * @return boolean
     */
    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    /**
     * load application instance
     *
     * @param App $app
     * @return void
     */
    public function load(App $app): void
    {
    }
}
