<p align="center">
<img src="https://minicli.dev/images/minicli_logo_term_pink.png" align="center" alt="logo" title="Minicli logo" alt="Minicli Logo" width="160">
</p>

<p align="center">
    <a href="//packagist.org/packages/minicli/minicli">
        <img src="https://poser.pugx.org/minicli/minicli/v" alt="Latest Stable Version" title="Latest Stable Version">
    </a>
    <a href="//packagist.org/packages/minicli/minicli">
        <img src="https://poser.pugx.org/minicli/minicli/downloads" alt="Total Downloads" title="Total Downloads">
    </a>
    <a href="//packagist.org/packages/minicli/minicli">
        <img src="https://poser.pugx.org/minicli/minicli/license" alt="License" title="License">
    </a>
    <a href="https://docs.minicli.dev/en/latest/?badge=latest">
        <img src="https://readthedocs.org/projects/minicliphp/badge/?version=latest" alt="Documentation Status" title="Documentation Status">
    </a>
    <h1 align="center">
        Minicli 3
    </h1>
</p>
<br>

[Minicli](https://docs.minicli.dev) is a minimalist, dependency-free framework for building CLI-centric PHP applications. It provides a structured way to organize your commands, as well as various helpers to facilitate working with command arguments, obtaining input from users, and printing colored output.

Quick links:

- [Documentation](https://docs.minicli.dev)
- [Demos](https://github.com/minicli/demos)
- [Contributing](CONTRIBUTING.md)
- [Contributors](CONTRIBUTORS.md)

## Dependency-free: What Does it Mean

What does it mean to be dependency-free? It means that you can build a working CLI PHP application without dozens of nested user-land dependencies. The basic `minicli/minicli` package has only **testing** dependencies, and a single system requirement:

- PHP >= 8

> Note: If you want to obtain user input, then the [`readline`](https://www.php.net/manual/en/function.readline.php) PHP extension is required as well.

It gives you a lot of room to choose your own dependencies.

## Getting Started

There are two ways to get started. If you want the bare minimum, what we'll call "Minimalist App", you can create a single PHP script with your whole application. If you want a more structured application, with commands and subcommands, then you should use Command Namespaces to organize your commands into Controllers.

### Minimalist App

If you just want to set up a few simple commands to run through `minicli`, all you need to do is to create an `App` and register your commands as anonymous functions.

1. Create an empty project
2. Run `composer require minicli/minicli` - this will generate a new `composer.json` file.
3. Create a `minicli` script with the following content:

```php
#!/usr/bin/env php
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

### Structured App (Recommended)

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

## Color Themes

Minicli supports the use of color themes to change the style of command line output. There is currently 1 built-in theme other than the default theme:

- Unicorn

To set the theme, pass in a configuration array with a `theme` value when initializing App in the script. Built-in themes need a leading `\` character:

```php
$app = new App([
    'theme' => '\Unicorn'
]);
```

To use the default built-in theme, do not include the theme configuration setting, or set it to an empty string.

User-defined themes can also be created and defined in your project. In this case, set the theme name including its namespace without a leading `\`:


```php
$app = new App([
    'theme' => 'App\Theme\Blue'
]);
```

The above setting would use the following example theme:

```php
<?php
// File: app/Theme/BlueTheme.php

namespace App\Theme;

use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\CLIColors;

class BlueTheme extends DefaultTheme
{
    public function getThemeColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_BLUE ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_BLUE ],
            'info'        => [ CLIColors::$FG_WHITE],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_BLUE ]
        ];
    }
}
```

User-defined themes only need to define styles which will override those in the default theme.

## Contributing

Contributions are very welcome! You can contribute with code, documentation, filing issues... Please refer to our [contributing doc](CONTRIBUTING.md) for more information on the contribution process and what we expect from you.

### Running the Test Suite

Minicli uses [Pest PHP](https://pestphp.com) as testing framework. Once you have all dependencies installed via `composer install`, you can run the test suite with:

```bash
./vendor/bin/pest
```

To obtain the code coverage report, you'll need to have `xdebug` installed. Then, you can run:

```bash
./vendor/bin/pest --coverage
```

And this will give you detailed information about code coverage.

## Building Minicli

The following tutorials on [dev.to](https://dev.to/erikaheidi) compose a series named "Building Minicli", where we create `minicli` from scratch:

 - Part 1: [Bootstrapping a CLI PHP Application in Vanilla PHP](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee) [ [minicli v.0.1.0](https://github.com/erikaheidi/minicli/tree/0.1.0) ]
 - Part 2: [Building minicli: Implementing Command Controllers](https://dev.to/erikaheidi/php-in-the-command-line-implementing-command-controllers-13lh) [ [minicli v.0.1.2](https://github.com/erikaheidi/minicli/tree/0.1.2) ]
 - Part 3: [Building minicli: Autoloading Command Namespaces](https://dev.to/erikaheidi/building-minicli-autoloading-command-namespaces-3ljm) [ [minicli v.0.1.3](https://github.com/erikaheidi/minicli/tree/0.1.3) ]
 - Part 4: [Introducing minicli: a microframework for CLI-centric PHP applications](https://dev.to/erikaheidi/introducing-minicli-a-microframework-for-cli-centric-php-applications-44ik)

_Note: Minicli has evolved a lot since that series was initially written, but that was the base for what Minicli is today._
