<?php

declare(strict_types=1);

use Minicli\DI\Container;
use Minicli\Exception\BindingResolutionException;

test('the container is a singleton')
    ->expect(fn () => Container::getInstance())
    ->toEqual(Container::getInstance());

it('can be resolve a class from instructions', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(
        abstract: SmtpMailer::class,
        concrete: fn () => new SmtpMailer('mail.example.com'),
    );

    expect(
        $container->make(SmtpMailer::class),
    )->toBeInstanceOf(SmtpMailer::class);
});

it('can resolve a class from an alias', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(
        abstract: 'mailer',
        concrete: fn () => new SmtpMailer('mail.example.com'),
    );

    expect(
        $container->make('mailer'),
    )->toBeInstanceOf(SmtpMailer::class);
});

it('can accept an implementation as the second parameter', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(
        abstract: MailerInterface::class,
        concrete: ArrayMailer::class,
    );

    expect(
        $container->make(MailerInterface::class),
    )->toBeInstanceOf(ArrayMailer::class);
});

it('can make a class we know nothing about - zero config resolution', function (): void {
    $container = Container::getInstance();
    $container->flush();

    expect(
        $container->make(ArrayMailer::class),
    )->toBeInstanceOf(ArrayMailer::class);
});

it('can resolve recursive dependencies', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(MailerInterface::class, SmtpMailer::class);
    $container->bind(SmtpMailer::class, fn () => new SmtpMailer('smtp.example.com'));

    expect(
        $container->make(MailerInterface::class),
    )->toBeInstanceOf(SmtpMailer::class);
});

it('can bind a singleton', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->singleton(SmtpMailer::class, fn () => new SmtpMailer('mail.example.com'));

    $smtpMailer1 = $container->make(SmtpMailer::class);
    $smtpMailer2 = $container->make(SmtpMailer::class);

    expect(
        $smtpMailer1,
    )->toEqual($smtpMailer2)->toBeInstanceOf(SmtpMailer::class);
});

it('can bind an instance as a singleton', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $instance = new ArrayMailer();
    $container->instance(ArrayMailer::class, $instance);

    expect(
        $container->make(ArrayMailer::class),
    )->toEqual($instance);
});

it('can inject dependencies', function (): void {
    $container = Container::getInstance();
    $container->flush();

    expect(
        $container->make(ApiMailer::class)
    )->toBeInstanceOf(ApiMailer::class);
});

it('can check for the existence of a binding', function (): void {
    $container = Container::getInstance();
    $container->flush();

    expect(
        $container->contains(ArrayMailer::class),
    )->toBeFalse();

    $container->bind(ArrayMailer::class);

    expect(
        $container->contains(ArrayMailer::class),
    )->toBeTrue();
});

it('will throw an exception when a target does not exist', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->make('test');
})->throws(BindingResolutionException::class);

it('will throw an exception when a class cannot be instantiated', function (): void {
    $container = Container::getInstance();
    $container->flush();

    $container->make(MakeBreak::class);
})->throws(BindingResolutionException::class);

interface MailerInterface
{
    public function send($message);
}

class ArrayMailer implements MailerInterface
{
    public function send($message)
    {
        // ..
    }
}

class SmtpMailer implements MailerInterface
{
    public function __construct(public string $server)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class ApiMailer implements MailerInterface
{
    public function __construct(public Api $api)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class Api
{
}

class MakeBreak
{
    private function __construct() {}

    public static function build(): static
    {
        return new static();
    }
}
