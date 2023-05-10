<?php

declare(strict_types=1);

use Minicli\Output\Adapter\FilePrinterAdapter;
use Minicli\Output\OutputHandler;

it('asserts that FilePrinterAdapter saves content to file', function () {
    $file_path = sys_get_temp_dir() . '/minicli-output-test.log';

    //makes sure we get a new empty file
    if (file_exists($file_path)) {
        @unlink($file_path);
    }

    (new OutputHandler(new FilePrinterAdapter($file_path)))
        ->rawOutput('writing output to file');

    expect(file_exists($file_path))->toBeTrue()
        ->and(file_get_contents($file_path))->toBe('writing output to file');
});

it('asserts that FilePrinterAdapter throws exception when a non-writable file is provided')
    ->expect(
        fn () => (new OutputHandler(
            new FilePrinterAdapter('/root/cant_write_here')
        ))->rawOutput('writing output to file')
    )
    ->throws(TypeError::class);
