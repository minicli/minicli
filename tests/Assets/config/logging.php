<?php

declare(strict_types=1);

use Minicli\Log\LogLevel;
use Minicli\Log\LogType;

return [
    /****************************************************************************
     * Logging Configuration
     * --------------------------------------------------------------------------
     *
     * This configuration defines the logging settings for your application.
     *****************************************************************************/

    'logging' => [
        'type' => LogType::SINGLE->value,

        'level' => LogLevel::INFO->value,

        'timestamp_format' => 'Y-m-d H:i:s',
    ],
];
