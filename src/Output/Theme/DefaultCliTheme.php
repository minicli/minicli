<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CliThemeInterface;
use Minicli\Output\CliColors;

class DefaultCliTheme implements CliThemeInterface
{
    public $default;
    public $alt;
    public $error;
    public $error_alt;
    public $success;
    public $success_alt;
    public $info;
    public $info_alt;

    public function __construct()
    {
        $this->loadColors();
    }

    public function loadColors()
    {
        $this->default     = [ CliColors::$FG_WHITE ];
        $this->alt         = [ CliColors::$FG_BLACK, CliColors::$BG_WHITE ];
        $this->error       = [ CliColors::$FG_RED ];
        $this->error_alt   = [ CliColors::$FG_WHITE, CliColors::$BG_RED ];
        $this->success     = [ CliColors::$FG_GREEN ];
        $this->success_alt = [ CliColors::$FG_WHITE, CliColors::$BG_GREEN ];
        $this->info        = [ CliColors::$FG_CYAN];
        $this->info_alt    = [ CliColors::$FG_WHITE, CliColors::$BG_CYAN ];
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorAlt()
    {
        return $this->error_alt;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getSuccessAlt()
    {
        return $this->success_alt;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getInfoAlt()
    {
        return $this->info_alt;
    }
}