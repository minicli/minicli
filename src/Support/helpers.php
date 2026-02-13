<?php

declare(strict_types=1);

function dd(mixed ...$args): never
{
    if ($args === []) {
        var_dump('miniCLI Debug');
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

function toPascalCase(string $value): string
{
    $normalized = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $value);
    $normalized = preg_replace('/[^a-zA-Z0-9]+/', ' ', (string) $normalized);

    $parts = preg_split('/\s+/', trim((string) $normalized));
    if ($parts === false || $parts === []) {
        return '';
    }

    return implode('', array_map(
        static fn (string $part): string => ucfirst(strtolower($part)),
        $parts,
    ));
}

function toSnakeCase(string $value): string
{
    $snake = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $value);
    $snake = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', (string) $snake);
    $snake = preg_replace('/[^a-zA-Z0-9]+/', '_', (string) $snake);

    return strtolower(trim((string) $snake, '_'));
}

function formatBytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $size = $bytes;
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }

    return number_format($size, 2) . ' ' . $units[$unitIndex];
}

function paddedString(string $tableCell, int $colSize = 5): string
{
    return mb_str_pad($tableCell, $colSize);
}
