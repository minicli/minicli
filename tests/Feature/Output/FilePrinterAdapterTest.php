<?php

use Minicli\Output\Adapter\FilePrinterAdapter;
use Minicli\Output\OutputHandler;

it('asserts that FilePrinterAdapter saves content to file', function () {
    $file_path = sys_get_temp_dir() . '/minicli-output-test.log';

    //makes sure we get a new empty file
    if (file_exists($file_path)) {
        @unlink($file_path);
    }

    $filePrinter = new FilePrinterAdapter($file_path);
    $output = new OutputHandler($filePrinter);

    $output->rawOutput("writing output to file");

    $this->assertEquals(file_get_contents($file_path), "writing output to file");
});

it('asserts that FilePrinterAdapter throws exception when a non-writable file is provided', function () {
    (new OutputHandler(
        new FilePrinterAdapter(
            '/root/cant_write_here'
        )
    ))->rawOutput('writing output to file');
})->expectException(TypeError::class);
