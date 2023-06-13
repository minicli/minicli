<?php

declare(strict_types=1);

use Assets\Services\TestService;

return [
    /****************************************************************************
     * Application Services
     * --------------------------------------------------------------------------
     *
     * The services to be loaded for your application.
     *****************************************************************************/

    'services' => [
        'test' => TestService::class,
    ],
];
