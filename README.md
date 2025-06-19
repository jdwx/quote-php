# jdwx/quote-php

A simple PHP module for handling quoted strings.

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/quote
```

Or download the source from GitHub: https://github.com/jdwx/quote-php.git

## Requirements

This module requires PHP 8.2 or later.

## Usage

This module provides functionality for processing arbitrary strings into lists of arguments, including support for quoting, $variable substitution, and implementing \`backtick\` replacement through callbacks.

See the parser.php file in the examples directory for a simple example.

## Stability

This module was refactored from existing code that has been widely used in production. It is considered stable and has complete test coverage.

## History

This module was refactored out of jdwx/args in June 2025.

