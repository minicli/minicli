<?php

declare(strict_types=1);

namespace Minicli\DI;

use ArrayAccess;
use Closure;
use Minicli\Exception\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @implements ArrayAccess<string,mixed>
 */
final class Container implements ArrayAccess
{
    protected static ?Container $instance = null;

    /**
     * @var array<string,array{concrete:Closure|string|null,shared:bool}>
     */
    protected array $bindings = [];

    /**
     * @var array<string,mixed>
     */
    private array $instances = [];

    /**
     * @return void
     */
    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function make(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;

        if ($concrete instanceof Closure || $concrete === $abstract) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function contains(string $abstract): bool
    {
        return array_key_exists($abstract, $this->bindings);
    }

    public function remove(string $abstract): void
    {
        unset($this->bindings[$abstract]);
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function build(Closure|string $concrete): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        if ( ! class_exists($concrete)) {
            throw new BindingResolutionException("Target class [{$concrete}] does not exist.", 0);
        }

        $reflector = new ReflectionClass($concrete);

        if ( ! $reflector->isInstantiable()) {
            throw new BindingResolutionException("Target [{$concrete}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (null === $constructor) {
            return new $concrete();
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * @param  array<ReflectionParameter>  $dependencies
     * @return array<int,mixed>
     *
     * @throws BindingResolutionException|ReflectionException
     */
    protected function resolveDependencies(array $dependencies): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // This is a much simpler version of what Laravel does
            $type = $dependency->getType(); // ReflectionType|null

            if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                $declaringClass = null === $dependency->getDeclaringClass() ? '' : $dependency->getDeclaringClass()->getName();
                throw new BindingResolutionException("Unresolvable dependency resolving [{$dependency}] in class {$declaringClass}");
            }

            $results[] = $this->make($type->getName());
        }

        return $results;
    }

    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * @param  string  $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->contains(
            abstract: $offset,
        );
    }

    /**
     * @param  string  $offset
     *
     * @throws BindingResolutionException|ReflectionException
     */
    public function offsetGet($offset): mixed
    {
        return $this->make(
            abstract: $offset,
        );
    }

    /**
     * @param  string  $offset
     * @param  Closure|null|string  $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->bind(
            abstract: $offset,
            concrete: $value,
        );
    }

    /**
     * @param  string  $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove(
            abstract: $offset,
        );
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function get(string $id): mixed
    {
        return $this->make(
            abstract: $id,
        );
    }

    public function has(string $id): bool
    {
        return $this->contains(
            abstract: $id,
        );
    }

    /**
     * @return array<string,array{concrete:Closure|string|null,shared:bool}>
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
