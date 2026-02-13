<?php

declare(strict_types=1);

namespace Minicli\Support;

use Minicli\App;

final class DiscoveryCache
{
    public const string FILE_NAME = 'minicli.json';

    private readonly string $cacheFile;

    /**
     * @var array<string, array<string, array{file: string, mtime: int, items: array<mixed>}>>
     */
    private array $cache = [];

    private bool $dirty = false;

    public function __construct(private readonly App $app)
    {
        $this->cacheFile = self::filePath($this->app);
        $this->cache = $this->loadCache();
    }

    public function __destruct()
    {
        $this->persist();
    }

    public static function filePath(App $app): string
    {
        return $app->discoveryPath() . '/' . self::FILE_NAME;
    }

    /**
     * @template T
     *
     * @param  callable(string): array<T>  $resolver
     * @return array<T>
     */
    public function rememberFile(string $domain, string $filePath, callable $resolver): array
    {
        $realPath = realpath($filePath);
        if ($realPath === false) {
            return [];
        }

        $mtime = filemtime($realPath);
        if ($mtime === false) {
            return [];
        }

        $cacheKey = sha1($realPath);
        $entry = $this->cache[$domain][$cacheKey] ?? null;

        if (
            is_array($entry)
            && $entry['file'] === $realPath
            && $entry['mtime'] === $mtime
        ) {
            /** @var array<T> $cachedItems */
            $cachedItems = $entry['items'];

            return $cachedItems;
        }

        $items = $resolver($realPath);
        $this->cache[$domain][$cacheKey] = [
            'file' => $realPath,
            'mtime' => $mtime,
            'items' => $items,
        ];
        $this->dirty = true;

        return $items;
    }

    private function persist(): void
    {
        if (! $this->dirty) {
            return;
        }

        $cacheDirectory = dirname($this->cacheFile);
        if (! is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0775, true);
        }

        file_put_contents($this->cacheFile, json_encode($this->cache, JSON_PRETTY_PRINT));
    }

    /**
     * @return array<string, array<string, array{file: string, mtime: int, items: array<mixed>}>>
     */
    private function loadCache(): array
    {
        if (! is_file($this->cacheFile)) {
            return [];
        }

        $contents = file_get_contents($this->cacheFile);
        if ($contents === false) {
            return [];
        }

        $cache = json_decode($contents, true);
        if (! is_array($cache)) {
            return [];
        }

        /** @var array<string, array<string, array{file: string, mtime: int, items: array<mixed>}>> $cache */
        return $cache;
    }
}
