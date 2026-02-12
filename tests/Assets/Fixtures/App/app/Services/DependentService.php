<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Services;

use Minicli\App;
use Minicli\Attributes\Service;
use Minicli\Contracts\ServiceInterface;
use Minicli\Log\Logger;

#[Service('dependent')]
final readonly class DependentService implements ServiceInterface
{
    public function __construct(
        private Logger $logger,
        private string $name = 'dependent-ok',
    ) {}

    public function load(App $app): void
    {
        $this->logger->debug('DependentService loaded');
    }

    public function name(): string
    {
        return $this->name;
    }
}
