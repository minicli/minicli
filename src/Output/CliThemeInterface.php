<?php

namespace Minicli\Output;


interface CliThemeInterface
{
    public function getDefault();

    public function getAlt();

    public function getError();

    public function getErrorAlt();

    public function getSuccess();

    public function getSuccessAlt();

    public function getInfo();

    public function getInfoAlt();
}