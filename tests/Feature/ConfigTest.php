<?php

use Minicli\Config;

it('asserts that config sets properties from constructor', function () {
    $config = new Config([
        "param1" => "value1",
        "param2" => "value2"
    ]);

    $this->assertTrue($config->has('param1'));
    $this->assertTrue($config->has('param2'));
});


it('asserts that config sets and gets properties', function () {
    $config = new Config([
        "param1" => "value1",
        "param2" => "value2"
    ]);

    $config->param3 = "value3";

    $this->assertEquals("value3", $config->param3);
});
