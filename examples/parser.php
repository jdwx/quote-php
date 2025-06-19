<?php


declare( strict_types = 1 );


require __DIR__ . '/../vendor/autoload.php';


use JDWX\Quote\StringParser;


( function () : void {

    $parse = StringParser::parseStringEx( 'The $foo \'brown $fox\' "jumps over" the `LAZY` dog.' );

    $parse->substVariables( [
        'foo' => 'quick',
        'fox' => 'nope',
    ] );
    $parse->substBackQuotes( fn( string $st ) => strtolower( $st ) );

    var_dump( $parse->getSegments() );


} )();