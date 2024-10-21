# Text UI
A simple and fast way to take user input and display output in text mode using PHP.

## Intro

Text UI is a development library that provides classes for text-based input and output.

It has two main categories of classes: `TextUI\Input` for user input, and 
`TextUI\Output` for displaying output to the user.

Usage is quite simple:

```php
$widget = new new TextUI\Input\TextEntry('Enter de text: ');
echo "You entered '{$widget->read()}'".PHP_EOL;
```


## Installation

The recommended installation method is via [Composer](https://getcomposer.org/):

`composer require php-tool-kit/text-ui`

## Examples

Look at the `examples` directory for basic usage examples.

## Documentation

See [Documentation page](https://php-tool-kit.github.io/text-ui/html/index.html).

## Licence

Library licensed under the MIT license (in the LICENCE file).


## Team

The team *soy yo*, [Everton da Rosa](https://everton3x@gmail.com).


## Contributing

Contributions are always welcome.

To do this, fork, create a branch, code, send a pool request.

Before submitting your contribution, preferably use PHPStan, PHPMD and PHPCS.

```sh
composer run phpstan
composer run phpmd
composer run phpcbf
composer run phpcs
```

It is also important that you create an issue explaining the purpose of the 
contribution and summarizing what you are doing, referencing this in the pool request.