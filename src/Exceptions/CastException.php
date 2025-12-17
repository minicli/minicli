<?php

declare(strict_types=1);

namespace Minicli\Exceptions;

use Exception;

final class CastException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Unable to cast property: {$property} - invalid value");
    }
}
