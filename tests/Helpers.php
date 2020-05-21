<?php

use Minicli\App;

function getCommandsPath()
{
    return __DIR__ . '/Assets/Command';
}
function getBasicApp()
{
    $config = [
        'app_path' => getCommandsPath(),
        'theme' => 'unicorn',
    ];

    return new App($config);
}
