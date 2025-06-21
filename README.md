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

A simple example using this module to get a list of arguments from a string:

```php

$parser = new Parser(
    hardQuote: QuoteOperator::simple(),
    delimiter: DelimiterOperator::whitespace(),
);

var_dump( iterator_to_array( $parser( 'The "quick brown fox" jumps \'over the\' lazy dog.' ) ) );
```

Produces:

```php
array(6) {
  [0] =>
  string(3) "The"
  [1] =>
  string(15) "quick brown fox"
  [2] =>
  string(5) "jumps"
  [3] =>
  string(8) "over the"
  [4] =>
  string(4) "lazy"
  [5] =>
  string(4) "dog."
}
```

A more complex example showing most of the features of this module:

```php
$fnVars = new Variables( [
    'foo' => 'quick',
    'fox' => 'nope',
] );
$parser = new Parser(
    comment: QuoteOperator::cComment(),
    hardQuote: QuoteOperator::single(),
    softQuote: QuoteOperator::double(),
    strongCallback: QuoteOperator::backtick(),
    weakCallback: QuoteOperator::varCurly(),
    openCallback: OpenEndedOperator::var(),
    escape: new MultiOperator( [ new HexEscape(), new ControlCharEscape() ] ),
    delimiter: ConsolidatedDelimiterOperator::whitespace(),

    # These are the functions used by callback operators.
    fnStrong: fn( string $st ) : string => strtolower( $st ),
    fnWeak: $fnVars,
    fnOpen: $fnVars
);

$st = 'The/* slow! */ $foo    \'brown $fox\' "jumps over" \\x74he `LAZY` dog.\n';
echo Segment::mergeValues( $parser->parse( $st ) );
```

Produces:

```
The quick brown $fox jumps over the lazy dog.
```

## Stability

This module was refactored from existing code that has been widely used in production. But then it experienced a near complete rewrite. It is considered stable and has complete test coverage.

## History

This module was refactored out of jdwx/args in June 2025.

