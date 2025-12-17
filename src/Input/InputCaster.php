<?php

declare(strict_types=1);

namespace Minicli\Input;

use BackedEnum;
use Minicli\Exceptions\CastException;
use UnitEnum;

final readonly class InputCaster
{
    /**
     * @return array<mixed>
     */
    public static function castToArray(string $value): array
    {
        if ($value === '') {
            return [];
        }

        return array_map(trim(...), explode(',', $value));
    }

    public static function castToBoolean(string $value): bool
    {
        return is_numeric($value)
            ? $value > 0
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param  class-string<UnitEnum|BackedEnum>  $target
     *
     * @throws CastException
     */
    public static function castToEnum(string $value, string $target): UnitEnum|BackedEnum
    {
        if (is_subclass_of($target, BackedEnum::class)) {
            /** @var ?BackedEnum $enum */
            $enum = $target::tryFrom($value);
            if ($enum !== null) {
                return $enum;
            }
        }

        if (is_subclass_of($target, UnitEnum::class)) {
            $valueLower = strtolower($value);
            foreach ($target::cases() as $case) {
                if (strtolower($case->name) === $valueLower) {
                    return $case;
                }
            }
        }

        throw new CastException($value);
    }

    /**
     * @throws CastException
     */
    public static function castToFloat(string $value): float
    {
        if (! is_numeric($value)) {
            throw new CastException($value);
        }

        return (float) $value;
    }

    /**
     * @throws CastException
     */
    public static function castToInteger(string $value): int
    {
        if (! is_numeric($value) || (string) (int) $value !== $value) {
            throw new CastException($value);
        }

        return (int) $value;
    }
}
