<?php

declare(strict_types=1);

function dd(mixed ...$args): never
{
    if ($args === []) {
        var_dump('🐛');
        exit();
    }

    foreach ($args as $arg) {
        var_dump($arg);
    }

    exit();
}

function envconfig(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    return $value === false ? $default : $value;
}

function toKebabCase(string $string): string
{
    $kebab = preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $string);
    $kebab = preg_replace('/([A-Z])([A-Z][a-z])/', '$1-$2', (string) $kebab);

    return strtolower(ltrim((string) $kebab, '-'));
}
