<?php

declare(strict_types=1);

namespace Minicli\Commands;

use Minicli\Attributes\Command;
use Minicli\Components\Alert;
use Minicli\Components\Select;
use Minicli\Components\Text;
use Minicli\Console\CommandRegistry;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Support\ConfigLoader;
use Minicli\Support\DiscoveryCache;
use Minicli\Support\ServiceLoader;
use ReflectionException;

#[Command(description: 'Manage discovery cache')]
final class Discovery extends ConsoleCommand
{
    public function default(): ExitCode
    {
        $selection = Select::make('Select a discovery command')
            ->options([
                'cache' => 'cache - Build or refresh discovery cache',
                'clear' => 'clear - Clear discovery cache',
            ])
            ->vertical()
            ->ask();

        return match ($selection) {
            'clear' => $this->clear(),
            default => $this->cache(),
        };
    }

    #[Command(description: 'Build or refresh discovery cache')]
    public function cache(): ExitCode
    {
        $cacheFile = DiscoveryCache::filePath($this->app);
        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }

        $this->warmDiscoveryCache();

        if (! is_file($cacheFile)) {
            Alert::make("Could not build cache file: {$cacheFile}")->error()->render();

            return ExitCode::Failure;
        }

        Text::make("Cache built: {$cacheFile}")->success()->render();

        return ExitCode::Success;
    }

    #[Command(description: 'Clear discovery cache')]
    public function clear(): ExitCode
    {
        $cacheFile = DiscoveryCache::filePath($this->app);

        if (! is_file($cacheFile)) {
            Alert::make("Cache file not found: {$cacheFile}")->warning()->render();

            return ExitCode::Success;
        }

        unlink($cacheFile);

        Text::make("Cache cleared: {$cacheFile}")->success()->render();

        return ExitCode::Success;
    }

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    private function warmDiscoveryCache(): void
    {
        new ConfigLoader()->load($this->app);
        new ServiceLoader()->load($this->app);
        $this->app->addService('commandRegistry', new CommandRegistry());
    }
}
