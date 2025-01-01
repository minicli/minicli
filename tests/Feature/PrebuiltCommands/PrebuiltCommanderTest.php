<?php

use Minicli\App;
use Minicli\PrebuiltCommands\HelpCommand;
use Minicli\PrebuiltCommands\PrebuiltCommander;

test('prebuilt commander is instantiated with app', function () {
    $app = new App();

    expect($app->prebuilt)->toBeInstanceOf(PrebuiltCommander::class);
});



test('help command is prebuilt', function () {
    $app = new App();

    $expected = new HelpCommand($app);

    expect($app->prebuilt->help)->toEqual($expected);
});

