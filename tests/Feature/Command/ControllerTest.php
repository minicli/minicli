<?php

it('asserts controller extracts arguments from command call', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test', 'params', 'name=erika', 'another', '--flag']);
})->expectOutputString(4);

it('asserts controller returns command call parameters', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test', 'params', 'name=erika', 'group=users', '--count-params']);
})->expectOutputString(2);

it('asserts controller extracts parameters from command call', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test', 'help', 'name=erika']);
})->expectOutputString('Hello erika');

it('asserts controller extracts flags from command call', function () {
    $app = getBasicApp();

    $app->runCommand(['minicli', 'test', 'help', 'name=erika', '--shout']);
})->expectOutputString('HELLO ERIKA');
