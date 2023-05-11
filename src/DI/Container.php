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
    /**
     * The Container Instance
     *
     * @var Container|null
     */
    protected static null|Container $instance = null;

    /**
     * The Container bindings, things that have been added to our container.
     * [
     *      'abstract class' => [
     *          'concrete' => 'concrete class',
     *          'shared' => 'true|false'
     *      ]
     * ]
     *
     * @var array[]
     */
    protected array $bindings = [];

    /**
     * Resolved Instances for the Container
     *
     * @var object[]
     */
    private array $instances = [];

    /**
     * Container constructor.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Get an Instance of the Container, or create a new instance
     *
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
     * Bind a new item into the Container
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $shared
     *
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
     * Bind a singleton into the Container
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Add a new built instance into the Container, for easy access
     *
     * @param string $abstract
     * @param mixed $instance
     *
     * @return void
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Retrieve an item from the Container, if possible.
     *
     * @param string $abstract
     *
     * @return mixed
     *
     * @throws BindingResolutionException|ReflectionException
     */
    public function make(string $abstract): mixed
    {
        // 1. If the type has already been resolved as a singleton, just return it
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // 2. Get the registered concrete resolver for this type, otherwise we'll assume we were passed a concretion that we can instantiate
        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;

        // 3. If the concrete is either a closure, or we didn't get a resolver, then we'll try to instantiate it.
        if ($concrete instanceof Closure || $concrete === $abstract) {
            $object = $this->build($concrete);
        }

        // 4. Otherwise the concrete must be referencing something else so we'll recursively resolve it until we get either a singleton instance, a closure, or run out of references and will have to try instantiating it.
        else {
            $object = $this->make($concrete);
        }

        // 5. If the class was registered as a singleton, we will hold the instance so we can always return it.
        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Check if the Container contains an item.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function contains(string $abstract): bool
    {
        return array_key_exists($abstract, $this->bindings);
    }

    /**
     * Remove an item from the Container
     *
     * @param string $abstract
     *
     * @return void
     */
    public function remove(string $abstract): void
    {
        unset($this->bindings[$abstract]);
    }

    /**
     * Build an item in the Container
     *
     * @param Closure|string $concrete
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws ReflectionException
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
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Build any dependencies for an item in the Container
     *
     * @param array $dependencies
     *
     * @return array
     *
     * @throws BindingResolutionException
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
     * Flush the contents of the container
     *
     * @return void
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * ArrayAccess: Check if the Container contains an item.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->contains(
            abstract: $offset,
        );
    }

    /**
     * ArrayAccess: Retrieve an item from the Container, if possible.
     *
     * @param string $offset
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function offsetGet($offset): mixed
    {
        return $this->make(
            abstract: $offset,
        );
    }

    /**
     * ArrayAccess: Bind a new item into the Container
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->bind(
            abstract: $offset,
            concrete: $value,
        );
    }

    /**
     * ArrayAccess: Remove an item from the Container
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->remove(
            abstract: $offset,
        );
    }

    /**
     * ContainerInterface: Retrieve an item from the Container, if possible.
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function get(string $id): mixed
    {
        return $this->make(
            abstract: $id,
        );
    }

    /**
     * ContainerInterface: Check if the Container contains an item.
     *
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