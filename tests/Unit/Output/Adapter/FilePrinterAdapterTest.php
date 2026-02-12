<?php

declare(strict_types=1);

use Minicli\Output\Adapter\FilePrinterAdapter;
use RuntimeException;

it('writes output to file', function (): void {
    $filePath = sys_get_temp_dir() . '/minicli-output-test.log';

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $adapter = new FilePrinterAdapter($filePath);
    $adapter->out('writing output to file');

    expect(file_exists($filePath))->toBeTrue()
        ->and(file_get_contents($filePath))->toBe('writing output to file');
});

it('throws when output file directory is not writable', function (): void {
    $adapter = new FilePrinterAdapter('/root/cant_write_here/minicli.log');
    $adapter->out('test');
})->throws(RuntimeException::class);
