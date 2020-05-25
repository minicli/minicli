# Minicli 2

[Minicli](https://github.com/minicli/minicli) is an experimental dependency-free toolkit for building CLI-centric applications in PHP.
Minicli was created as [an educational experiment](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee) and a way to go dependency-free when building simple command-line applications in PHP. It can be used for microservices, personal dev tools, bots and little fun things.

Minicli 2 has a lot of improved features, and about 98% test coverage with [Pest PHP](https://pestphp.com/).

## Dependency-free: What Does it Mean

What does it mean to be dependency-free? It means that you can build a working CLI PHP application without dozens of nested user-land dependencies. The basic `minicli/minicli` package has only **testing** dependencies, and a couple system requirements:

- PHP >= 7.2
- `ext-readline` for obtaining user input

It gives you a lot of room to choose your own dependencies.

## Getting Started

There are two ways to get started. If you want the **bare minimum**:

### Minimalist App

If you just want to set up a few simple commands to run through `minicli`, all you need to do is to create an `App` and register your commands as anonymous functions.

1. Create an empty project
2. Run `composer require minicli/minicli` - this will generate a new `composer.json` file.
3. Create a `minicli` script with the following content:

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Minicli\App;
use Minicli\Command\CommandCall;

$app = new App();
$app->setSignature('./minicli mycommand');

$app->registerCommand('mycommand', function(CommandCall $input) {
    echo "My Command!";

    var_dump($input);
});

$app->runCommand($argv);
``` 

Then, make it executable and run `minicli` with your command:


```bash
chmod +x minicli
./minicli mycommand
```

### Structured App

For a more structured application using Controllers and Services, it's best to use [Command Namespaces](https://minicliphp.readthedocs.io/en/latest/#using-command-controllers).
Our [application template repository](https://github.com/minicli/application) is a great starting point / template to set up Minicli that way.

To create a new project using the `minicli/application` template, run:

```
composer create-project --prefer-dist minicli/application myapp
```

This will generate a directory structure like the following:

```
.
app
└── Command
    └── Help
        ├── DefaultController.php
        ├── TableController.php
        └── TestController.php
├── composer.json
├── docs
├── LICENSE
├── minicli
├── mkdocs.yml
└── README.md

```

Each directory inside `app/Command` represents a Command Namespace.
The classes inside `app/Command/Help` represent subcommands that you can access through the main `help` command.

You can now run the boostrapped application with:

```bash
./minicli help
```

The [documentation](https://docs.minicli.dev) contains more detailed information about creating commands and working with output.

## Building Minicli

The following tutorials on [dev.to](https://dev.to/erikaheidi) compose a series named "Building Minicli", where we create `minicli` from scratch:

 - Part 1: [Bootstrapping a CLI PHP Application in Vanilla PHP](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee) [ [minicli v.0.1.0](https://github.com/erikaheidi/minicli/tree/0.1.0) ]
 - Part 2: [Building minicli: Implementing Command Controllers](https://dev.to/erikaheidi/php-in-the-command-line-implementing-command-controllers-13lh) [ [minicli v.0.1.2](https://github.com/erikaheidi/minicli/tree/0.1.2) ]
 - Part 3: [Building minicli: Autoloading Command Namespaces](https://dev.to/erikaheidi/building-minicli-autoloading-command-namespaces-3ljm) [ [minicli v.0.1.3](https://github.com/erikaheidi/minicli/tree/0.1.3) ]
 - Part 4: [Introducing minicli: a microframework for CLI-centric PHP applications](https://dev.to/erikaheidi/introducing-minicli-a-microframework-for-cli-centric-php-applications-44ik)

_Note: Minicli has evolved a lot since that series was initially written, but that was the base for what Minicli is today._

## Created with Minicli

- [Dolphin](https://github.com/do-community/dolphin) - a CLI tool for managing DigitalOcean servers with Ansible.
