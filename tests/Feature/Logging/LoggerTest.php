<?php

declare(strict_types=1);

beforeEach(function (): void {
    $this->app = getConfiguredApp();
    $this->logPath = "{$this->app->logs_path}/minicli.log";
});

it('asserts App can log data', function (): void {
    $this->app->logger->log('Testing minicli');

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('INFO: Testing minicli');
});

it('asserts App can log data with INFO level', function (): void {
    $this->app->logger->info('Testing minicli INFO logging');

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('INFO: Testing minicli INFO logging');
});

it('asserts App can log data with WARNING level', function (): void {
    $this->app->logger->warning('Testing minicli WARNING logging');

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('WARNING: Testing minicli WARNING logging');
});

it('asserts App can log data with ERROR level', function (): void {
    $this->app->logger->error('Testing minicli ERROR logging');

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('ERROR: Testing minicli ERROR logging');
});

it('asserts App can log data with DEBUG level', function (): void {
    $this->app->logger->debug('Testing minicli DEBUG logging');

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('DEBUG: Testing minicli DEBUG logging');
});

it('asserts App can log data with context', function (): void {
    $this->app->logger->log('Testing minicli context logging', ['context' => 'test']);

    $log = file_get_contents($this->logPath);

    expect($log)->toContain('INFO: Testing minicli context logging - {"context":"test"}');
});
