<?php


declare( strict_types = 1 );


require __DIR__ . '/../vendor/autoload.php';


use JDWX\Quote\Operators\ConsolidatedDelimiterOperator;
use JDWX\Quote\Operators\Escape\HexEscape;
use JDWX\Quote\Operators\OpenEndedOperator;
use JDWX\Quote\Operators\QuoteOperator;
use JDWX\Quote\Parser;
use JDWX\Quote\Segment;
use JDWX\Quote\Variables;


( function () : void {

    $comment = QuoteOperator::comment();
    $hardQuote = QuoteOperator::single();
    $softQuote = QuoteOperator::double();
    $strongCallback = QuoteOperator::backtick();
    $weakCallback = QuoteOperator::varCurly();
    $openCallback = OpenEndedOperator::var();
    $delim = ConsolidatedDelimiterOperator::whitespace();
    $escape = new HexEscape();
    $fnStrong = fn( string $st ) : string => strtolower( $st );
    $fnVars = new Variables( [
        'foo' => 'quick',
        'fox' => 'nope',
    ] );
    $parser = new Parser(
        $comment,
        $hardQuote,
        $softQuote,
        $strongCallback,
        $weakCallback,
        $openCallback,
        $escape,
        $delim,
        $fnStrong,
        $fnVars,
        $fnVars
    );

    $st = 'The/* slow! */ $foo    \'brown $fox\' "jumps over" the `LAZY` dog.';
    echo Segment::mergeValues( $parser->parse( $st ) ), "\n";

} )();