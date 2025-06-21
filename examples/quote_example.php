<?php


declare( strict_types = 1 );


use JDWX\Quote\Operators\DelimiterOperator;
use JDWX\Quote\Operators\QuoteOperator;
use JDWX\Quote\Parser;


require __DIR__ . '/../vendor/autoload.php';


( function () : void {

    # Sometimes it's "look, I just want to parse a string as arguments, okay?"
    # That's how it always starts, anyway...

    $parser = new Parser(
        hardQuote: QuoteOperator::simple(),
        delimiter: DelimiterOperator::whitespace(),
    );

    var_dump( iterator_to_array( $parser( 'The "quick brown fox" jumps \'over the\' lazy dog.' ) ) );

} )();

