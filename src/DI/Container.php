<?php

declare(strict_types=1);

namespace Minicli\DI;

use ArrayAccess;
use Closure;
use Minicli\Exception\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class Container implements ArrayAccess
{
    protected static null|Container $instance = null;

    /**
     * @var array[]
     */
    protected array $bindings = [];

    /**
     * @var object[]
     */
    private array $instances = [];

    /**
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    /**
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @param string $abstract
     * @param mixed $instance
     * @return void
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * @param string $abstract
     * @return mixed
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

    /**
     * @param string $abstract
     * @return bool
     */
    public function contains(string $abstract): bool
    {
        return array_key_exists($abstract, $this->bindings);
    }

    /**
     * @param string $abstract
     * @return void
     */
    public function remove(string $abstract): void
    {
        unset($this->bindings[$abstract]);
    }

    /**
     * @param Closure|string $concrete
     * @return mixed
     * @throws BindingResolutionException|ReflectionException
     */
    public function build(Closure|string $concrete): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new BindingResolutionException("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete();
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * @param array $dependencies
     * @return array<int,mixed>
     * @throws BindingResolutionException|ReflectionException
     */
    protected function resolveDependencies(array $dependencies): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // This is a much simpler version of what Laravel does
            $type = $dependency->getType(); // ReflectionType|null

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new BindingResolutionException("Unresolvable dependency resolving [$dependency] in class {$dependency->getDeclaringClass()->getName()}");
            }

            $results[] = $this->make($type->getName());
        }

        return $results;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->contains(
            abstract: $offset,
        );
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws BindingResolutionException|ReflectionException
     */
    public function offsetGet($offset): mixed
    {
        return $this->make(
            abstract: $offset,
        );
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->bind(
            abstract: $offset,
            concrete: $value,
        );
    }

    /**
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->remove(
            abstract: $offset,
        );
    }

    /**
     * @param string $id
     * @return mixed
     * @throws BindingResolutionException|ReflectionException
     */
    public function get(string $id): mixed
    {
        return $this->make(
            abstract: $id,
        );
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->contains(
            abstract: $id,
        );
    }
}
