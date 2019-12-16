# minicli

[Minicli](https://github.com/minicli/minicli) is an experimental dependency-free toolkit for building CLI-only applications in PHP, created by [erikaheidi](https://twitter.com/erikaheidi).
Minicli was created as [an educational experiment](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee) and a way to go dependency-free when building simple command-line applications in PHP. It can be used for microservices, personal dev tools, bots and little fun things.

## Getting Started

You can use our [application template repository](https://github.com/minicli/application) for creating a new app with Minicli.

To create a new project using Composer, run:

```
composer create-project --prefer-dist minicli/application myapp
```

Demos coming soon.

## Building MiniCLI

The following tutorials on [dev.to](https://dev.to/erikaheidi) compose a series named "Building Minicli", where we create `minicli` from scratch:

 - Part 1: [Bootstrapping a CLI PHP Application in Vanilla PHP](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee) [ [minicli v.0.1.0](https://github.com/erikaheidi/minicli/tree/0.1.0) ]
 - Part 2: [Building minicli: Implementing Command Controllers](https://dev.to/erikaheidi/php-in-the-command-line-implementing-command-controllers-13lh) [ [minicli v.0.1.2](https://github.com/erikaheidi/minicli/tree/0.1.2) ]
 - Part 3: [Building minicli: Autoloading Command Namespaces](https://dev.to/erikaheidi/building-minicli-autoloading-command-namespaces-3ljm) [ [minicli v.0.1.3](https://github.com/erikaheidi/minicli/tree/0.1.3) ]
 - Part 4: *soon*

## Created with Minicli

- [Dolphin](https://github.com/do-community/dolphin) - a CLI tool for managing DigitalOcean servers with Ansible.
