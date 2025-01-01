<?php

namespace Minicli\PrebuiltCommands;

interface IsRegisterableCommand
{
    public function register(): void;
    public function registerCallback(callable $customCode, array $customCodeArgs = []);
}
