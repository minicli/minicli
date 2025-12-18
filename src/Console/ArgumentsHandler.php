<?php

declare(strict_types=1);

namespace Minicli\Console;

use Exception;
use Minicli\Attributes\Argument;
use Minicli\Exceptions\CastException;
use Minicli\Exceptions\MissingParametersException;
use Minicli\Input\InputCaster;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/** @internal */
final readonly class ArgumentsHandler
{
    public function __construct(
        /** @var array<ReflectionParameter> */
        public array $parameters,
    ) {}

    /**
     * @return array<ArgumentInfo>
     *
     * @throws ReflectionException
     */
    public function extractArgumentInfo(): array
    {
        $argumentsInfo = [];

        foreach ($this->parameters as $parameter) {
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();
            $isNullable = $type->allowsNull();
            $hasDefault = $parameter->isDefaultValueAvailable();

            $argumentAttributes = $parameter->getAttributes(Argument::class);
            $argumentAttribute = empty($argumentAttributes)
                ? null
                : $argumentAttributes[0]->newInstance();

            $name = $argumentAttribute && $argumentAttribute->name !== ''
                ? $argumentAttribute->name
                : toKebabCase($parameter->getName());

            if ($typeName === 'bool' && ! str_starts_with($name, '--')) {
                $name = '--' . $name;
            }

            $argumentsInfo[] = new ArgumentInfo(
                name: $name,
                description: $argumentAttribute->description ?? '',
                required: $typeName === 'bool' ? false : (! $isNullable && ! $hasDefault),
                default: $typeName === 'bool'
                    ? ($hasDefault && $parameter->getDefaultValue() === true)
                    : ($hasDefault ? $parameter->getDefaultValue() : null),
            );
        }

        return $argumentsInfo;
    }

    /**
     * @return array<mixed>
     *
     * @throws MissingParametersException|CastException|ReflectionException
     */
    public function prepareArguments(CommandCall $input): array
    {
        $arguments = [];
        $missing = [];

        foreach ($this->parameters as $parameter) {
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();
            $isNullable = $type->allowsNull();
            $hasDefault = $parameter->isDefaultValueAvailable();

            $argumentAttributes = $parameter->getAttributes(Argument::class);
            $argumentAttribute = empty($argumentAttributes)
                ? null
                : $argumentAttributes[0]->newInstance();

            $kebabName = $argumentAttribute && $argumentAttribute->name !== ''
                ? $argumentAttribute->name
                : toKebabCase($parameter->getName());

            if ($typeName === 'bool') {
                $arguments[] = $input->hasFlag($kebabName);

                continue;
            }

            $hasValue = $input->hasParam($kebabName);
            if (! $hasValue && ! $isNullable && ! $hasDefault) {
                $missing[] = $kebabName;

                continue;
            }

            // If parameter is missing but optional, use default or null
            if (! $hasValue) {
                $arguments[] = $hasDefault
                    ? $parameter->getDefaultValue()
                    : null;

                continue;
            }

            $value = $input->getParam($kebabName);
            try {
                $arguments[] = InputCaster::castValue($value, $typeName);
            } catch (Exception) {
                throw new CastException($kebabName);
            }
        }

        if ($missing !== []) {
            throw new MissingParametersException($missing);
        }

        return $arguments;
    }
}
