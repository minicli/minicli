<?php

declare(strict_types=1);

/**
 * @param  array<string, mixed>  $defaultConfig
 * @return array<string, mixed>
 */
function load_config(array $defaultConfig, string $configPath): array
{
    $config = [];

    foreach ((array) glob("{$configPath}/*.php") as $configFile) {
        $configData = include $configFile;
        if (is_array($configData)) {
            $config = [...$config, ...$configData];
        }
    }

    return [...$defaultConfig, ...$config];
}

function envconfig(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    return $value === false ? $default : $value;
}
