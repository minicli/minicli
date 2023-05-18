<?php

declare(strict_types=1);

use Minicli\Config;

it('asserts that config sets properties from constructor', function (): void {
    $config = new Config([
        "param1" => "value1",
        "param2" => "value2"
    ]);

    expect($config->param1)->toBe("value1")
        ->and($config->param2)->toBe("value2");
});


it('asserts that config sets and gets properties', function (): void {
    $config = new Config([
        "param1" => "value1",
        "param2" => "value2"
    ]);

    $config->param3 = "value3";

    expect($config->param1)->toBe("value1")
        ->and($config->param2)->toBe("value2")
        ->and($config->param3)->toBe("value3");
});
