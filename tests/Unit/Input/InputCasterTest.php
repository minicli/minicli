<?php

declare(strict_types=1);

use Minicli\Exceptions\CastException;
use Minicli\Input\InputCaster;

it('casts scalar and array values', function (): void {
    expect(InputCaster::castValue('10', 'int'))->toBe(10)
        ->and(InputCaster::castValue('+1', 'int'))->toBe(1)
        ->and(InputCaster::castValue('01', 'int'))->toBe(1)
        ->and(InputCaster::castValue('3.14', 'float'))->toBe(3.14)
        ->and(InputCaster::castValue('hello', 'string'))->toBe('hello')
        ->and(InputCaster::castValue('a,b,c', 'array'))->toBe(['a', 'b', 'c'])
        ->and(InputCaster::castToBoolean('1'))->toBeTrue();
});

it('casts enum values', function (): void {
    expect(InputCaster::castValue('open', InputCasterStatus::class))
        ->toBe(InputCasterStatus::Open);
});

it('throws on invalid casts', function (): void {
    InputCaster::castValue('ten', 'int');
})->throws(CastException::class);

enum InputCasterStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
