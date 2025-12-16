<?php

declare(strict_types=1);

function envconfig(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    return $value === false ? $default : $value;
}
