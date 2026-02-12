<?php

declare(strict_types=1);

use Minicli\Output\Adapter\DefaultPrinterAdapter;

it('returns message unchanged in default printer adapter', function (): void {
    $adapter = new DefaultPrinterAdapter();

    expect($adapter->out('hello'))->toBe('hello');
});
