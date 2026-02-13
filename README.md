<p align="center">
<img src="https://docs.minicli.dev/en/latest/images/logo/minicli_logo_term_pink.png" align="center" alt="logo" title="Minicli logo" alt="Minicli Logo" width="160">
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
        miniCLI
    </h1>
    <h4 align="center">
        Minimalist, dependency-free framework for building CLI-centric PHP applications
    </h4>
</p>
<hr>

[miniCLI](https://docs.minicli.dev) is a minimalist, dependency-free framework for building CLI-centric PHP applications. It provides a structured way to organize your commands, as well as various helpers to facilitate working with command arguments, obtaining input from users, and printing colored output.

Quick links:

- [Documentation](https://docs.minicli.dev)
- [Contributing](CONTRIBUTING.md)
- [Contributors](CONTRIBUTORS.md)

## Dependency-free: What Does it Mean

What does it mean to be dependency-free? It means that you can build a working CLI PHP application without dozens of nested user-land dependencies. The basic `minicli/minicli` package has only **testing** dependencies, and a single system requirement:

- PHP >= 8.4

It gives you a lot of room to choose your own dependencies.

## Getting Started

To create a new project using our miniCLI template, run:

```
composer create-project --prefer-dist minicli/app myapp
```

This will generate a minimal application with everything you need to create a CLI application with miniCLI.

You can now run the bootstrapped application with:

```bash
./minicli help
```

The [documentation](https://docs.minicli.dev) contains more detailed information about creating commands and the full features provided by miniCLI.

## Contributing

Contributions are very welcome! You can contribute with code, documentation, filing issues... Please refer to our [contributing doc](CONTRIBUTING.md) for more information on the contribution process and what we expect from you.

### Running the Test Suite

miniCLI uses [Pest PHP](https://pestphp.com) as testing framework. Once you have all dependencies installed via `composer install`, you can run the complete test suite with:

```bash
composer test
```
