<?php

declare(strict_types=1);

namespace Minicli;

class Config implements ServiceInterface
{
    /**
     * config array
     *
     * @var array
     */
    protected array $config;

    /**
     * Config constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * get config
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    /**
     * set config
     *
     * @param string $name
     * @param string $value
     */
    public function __set(string $name, string $value): void
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
