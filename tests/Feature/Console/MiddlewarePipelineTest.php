<?php

declare(strict_types=1);

it('executes command middlewares in pipeline order', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'middleware-test']);

    expect($result)->toBe(0);
})->expectOutputString('prefix>suffix>command<suffix<prefix');

it('allows middleware to short-circuit command execution', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'middleware-block']);

    expect($result)->toBe(2);
})->expectOutputString('blocked');

it('throws when middleware does not implement middleware interface', function (): void {
    getConfiguredApp()->runCommand(['minicli', 'middleware-invalid']);
})->throws(RuntimeException::class, "Middleware 'Assets\\Fixtures\\App\\app\\Middlewares\\InvalidMiddleware' must implement Minicli\\Contracts\\MiddlewareInterface.");

it('injects middleware configuration from middleware attribute', function (): void {
    $result = getConfiguredApp()->runCommand(['minicli', 'middleware-configured']);

    expect($result)->toBe(0);
})->expectOutputString('retry(3,2000)>command<retry');
