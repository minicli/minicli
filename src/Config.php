<?php

namespace Minicli;

class Config implements ServiceInterface
{
    /** @var  array */
    protected $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->config[$name] = $value;
    }

    public function has($name)
    {
        return isset($this->config[$name]);
    }

    public function load(App $app)
    {
        return null;
    }
}
