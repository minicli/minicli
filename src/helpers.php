<?php

declare(strict_types=1);

/**
 * @param array<string, mixed> $defaultConfig
 * @return array<string, mixed>
 */
function load_config(array $defaultConfig): array
{
    $config = [];

    foreach ((array) glob(__DIR__.'/../config/*.php') as $configFile) {
        $configData = include $configFile;
        if (is_array($configData)) {
            $config = array_merge($config, $configData);
        }
    }

    return array_merge($defaultConfig, $config);
}

function envconfig(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return false === $value ? $default : $value;
}
