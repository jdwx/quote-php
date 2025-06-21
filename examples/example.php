<?php


declare( strict_types = 1 );


require __DIR__ . '/../vendor/autoload.php';


use JDWX\Quote\Operators\ConsolidatedDelimiterOperator;
use JDWX\Quote\Operators\Escape\ControlCharEscape;
use JDWX\Quote\Operators\Escape\HexEscape;
use JDWX\Quote\Operators\MultiOperator;
use JDWX\Quote\Operators\OpenEndedOperator;
use JDWX\Quote\Operators\QuoteOperator;
use JDWX\Quote\Parser;
use JDWX\Quote\Segment;
use JDWX\Quote\Variables;


( function () : void {

    $fnVars = new Variables( [
        'foo' => 'quick',
        'fox' => 'nope',
    ] );
    $parser = new Parser(
    # Configure the parser to use the operators we want.
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

} )();