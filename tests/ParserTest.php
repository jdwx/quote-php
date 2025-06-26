<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Operators\DelimiterOperator;
use JDWX\Quote\Operators\Escape\ControlCharEscape;
use JDWX\Quote\Operators\Escape\HexEscape;
use JDWX\Quote\Operators\MultiOperator;
use JDWX\Quote\Operators\OpenEndedOperator;
use JDWX\Quote\Operators\QuoteOperator;
use JDWX\Quote\Parser;
use JDWX\Quote\Segment;
use JDWX\Quote\SegmentType;
use JDWX\Quote\Variables;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Parser::class )]
final class ParserTest extends TestCase {


    public static function assertSegment( SegmentType $i_type, string $i_value, string $i_original,
                                          Segment     $i_segment ) : void {
        self::assertSame( $i_type, $i_segment->type, 'type mismatch' );
        self::assertSame( $i_value, $i_segment->value, 'value mismatch' );
        self::assertSame( $i_original, $i_segment->original, 'original mismatch' );
    }


    public static function testInvoke() : void {
        $parser = new Parser(
            hardQuote: QuoteOperator::double(),
            delimiter: DelimiterOperator::whitespace(),
        );
        $st = 'Foo "Bar Baz" Qux';
        self::assertSame( [ 'Foo', 'Bar Baz', 'Qux' ], iterator_to_array( $parser( $st ) ) );
    }


    public function testParseForComment() : void {
        $comment = new QuoteOperator( '/*', '*/' );
        $parser = new Parser( comment: $comment );
        $st = 'Foo /* Bar */ Baz /* Qux */ Garply';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 5, $r );

        self::assertSegment( SegmentType::LITERAL, 'Foo ', 'Foo ', $r[ 0 ] );
        self::assertSegment( SegmentType::COMMENT, '', '/* Bar */', $r[ 1 ] );
        self::assertSegment( SegmentType::LITERAL, ' Baz ', ' Baz ', $r[ 2 ] );
        self::assertSegment( SegmentType::COMMENT, '', '/* Qux */', $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, ' Garply', ' Garply', $r[ 4 ] );
    }


    public function testParseForComplex() : void {
        $parser = $this->makeComplexParser();
        $st = 'Foo /* Bar "Baz" */ "$foo $baz" ${foo} `qux ${foo} ${baz} qux` \'quux\'';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 11, $r );
        self::assertSegment( SegmentType::LITERAL, 'Foo', 'Foo', $r[ 0 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 1 ] );
        self::assertSegment( SegmentType::COMMENT, '', '/* Bar "Baz" */', $r[ 2 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 3 ] );
        self::assertSegment( SegmentType::SOFT_QUOTED, ' qux', '"$foo $baz"', $r[ 4 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 5 ] );
        self::assertSegment( SegmentType::WEAK_CALLBACK, 'bar', '${foo}', $r[ 6 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 7 ] );
        self::assertSegment( SegmentType::STRONG_CALLBACK, 'QUX BAR  QUX', '`qux ${foo} ${baz} qux`', $r[ 8 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 9 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'quux', '\'quux\'', $r[ 10 ] );
    }


    public function testParseForDelimiter() : void {
        $delimiter = DelimiterOperator::whitespace();
        $softQuote = new QuoteOperator( '"', '"' );
        $parser = new Parser( softQuote: $softQuote, delimiter: $delimiter );
        $st = 'Foo "Bar Baz"   Qux';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertSegment( SegmentType::LITERAL, 'Foo', 'Foo', $r[ 0 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 1 ] );
        self::assertSegment( SegmentType::SOFT_QUOTED, 'Bar Baz', '"Bar Baz"', $r[ 2 ] );
        self::assertSegment( SegmentType::DELIMITER, '   ', '   ', $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, 'Qux', 'Qux', $r[ 4 ] );
        self::assertCount( 5, $r );
    }


    public function testParseForEscaping() : void {
        $escape = new ControlCharEscape();
        $parser = new Parser( escape: $escape );
        $st = 'Foo \n Bar \t Baz \r Qux';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 1, $r );
        $segment = $r[ 0 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::LITERAL, $segment->type );
        self::assertSame( "Foo \n Bar \t Baz \r Qux", $segment->value );
    }


    public function testParseForHardQuote() : void {
        $hardQuote = new QuoteOperator( "'", "'" );
        $escape = new ControlCharEscape();
        $parser = new Parser( hardQuote: $hardQuote, escape: $escape );
        $st = "Foo 'Bar' Baz 'Qux \\n Quux' \\n Corge";
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 5, $r );

        self::assertSegment( SegmentType::LITERAL, 'Foo ', 'Foo ', $r[ 0 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Bar', "'Bar'", $r[ 1 ] );
        self::assertSegment( SegmentType::LITERAL, ' Baz ', ' Baz ', $r[ 2 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Qux \n Quux', "'Qux \\n Quux'", $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, " \n Corge", ' \n Corge', $r[ 4 ] );
    }


    public function testParseForHardQuoteMultiple() : void {
        $hardQuote = QuoteOperator::double();
        $delimiter = DelimiterOperator::whitespace();
        $parser = new Parser( hardQuote: $hardQuote, delimiter: $delimiter );
        $st = 'Foo "Bar" "Baz" "Qux"';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertSegment( SegmentType::LITERAL, 'Foo', 'Foo', $r[ 0 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 1 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Bar', '"Bar"', $r[ 2 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 3 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Baz', '"Baz"', $r[ 4 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 5 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Qux', '"Qux"', $r[ 6 ] );
        self::assertCount( 7, $r );
    }


    public function testParseForHardQuoteMultiple2() : void {
        $hardQuote = QuoteOperator::double();
        $delimiter = DelimiterOperator::whitespace();
        $parser = new Parser( hardQuote: $hardQuote, delimiter: $delimiter );
        $st = '"" "Foo" ""';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertSegment( SegmentType::HARD_QUOTED, '', '""', $r[ 0 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 1 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, 'Foo', '"Foo"', $r[ 2 ] );
        self::assertSegment( SegmentType::DELIMITER, ' ', ' ', $r[ 3 ] );
        self::assertSegment( SegmentType::HARD_QUOTED, '', '""', $r[ 4 ] );
        self::assertCount( 5, $r );
    }


    public function testParseForNoOperators() : void {
        $parser = new Parser();
        $st = 'Foo "Bar" \'Baz\' ${Qux} `Quux` Garply';
        $r = iterator_to_array( $parser->parse( $st ) );
        self::assertCount( strlen( $st ), $r );
        $r = iterator_to_array( Segment::coalesce( $r ) );
        self::assertCount( 1, $r );
        self::assertSegment( SegmentType::LITERAL, $st, $st, $r[ 0 ] );
    }


    public function testParseForStrongCallback() : void {
        $hardQuote = new QuoteOperator( "'", "'" );
        $softQuote = new QuoteOperator( '"', '"' );
        $strongCallback = new QuoteOperator( '`', '`' );
        $escape = new ControlCharEscape();
        $parser = new Parser( hardQuote: $hardQuote, softQuote: $softQuote,
            strongCallback: $strongCallback, escape: $escape );
        $st = 'Foo `Bar` Baz `\'Qux\' \n "Quux"` \n Corge';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 5, $r );

        self::assertSegment( SegmentType::LITERAL, 'Foo ', 'Foo ', $r[ 0 ] );
        self::assertSegment( SegmentType::STRONG_CALLBACK, 'Bar', '`Bar`', $r[ 1 ] );
        self::assertSegment( SegmentType::LITERAL, ' Baz ', ' Baz ', $r[ 2 ] );
        self::assertSegment( SegmentType::STRONG_CALLBACK, "Qux \n \"Quux\"",
            '`\'Qux\' \n "Quux"`', $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, " \n Corge", ' \n Corge', $r[ 4 ] );
    }


    public function testParseForStrongCallbackWithVariable() : void {
        $strongCallback = new QuoteOperator( '`', '`' );
        $weakCallback = new QuoteOperator( '${', '}' );
        $vars = new Variables( [ 'foo' => 'bar' ] );
        $fnStrong = function ( string $i_stValue ) : string {
            return '>>>' . $i_stValue . '<<<';
        };
        $parser = new Parser( strongCallback: $strongCallback, weakCallback: $weakCallback, fnStrong: $fnStrong,
            fnWeak: $vars );
        $st = '`Bar ${foo} Baz ${qux}`';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertSegment( SegmentType::STRONG_CALLBACK, '>>>Bar bar Baz <<<', '`Bar ${foo} Baz ${qux}`', $r[ 0 ] );
        self::assertCount( 1, $r );
    }


    public function testParserForSoftQuote() : void {
        $softQuote = new QuoteOperator( '"', '"' );
        $escape = new ControlCharEscape();
        $parser = new Parser( softQuote: $softQuote, escape: $escape );
        $st = 'Foo "Bar" Baz "Qux \n Quux" \n Corge';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 5, $r );

        self::assertSegment( SegmentType::LITERAL, 'Foo ', 'Foo ', $r[ 0 ] );
        self::assertSegment( SegmentType::SOFT_QUOTED, 'Bar', '"Bar"', $r[ 1 ] );
        self::assertSegment( SegmentType::LITERAL, ' Baz ', ' Baz ', $r[ 2 ] );
        self::assertSegment( SegmentType::SOFT_QUOTED, "Qux \n Quux", '"Qux \\n Quux"', $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, " \n Corge", ' \n Corge', $r[ 4 ] );
    }


    public function testParserForWeakCallback() : void {
        $weakCallback = new QuoteOperator( '${', '}' );
        $escape = new ControlCharEscape();
        $parser = new Parser( weakCallback: $weakCallback, escape: $escape );
        $st = 'Foo ${Bar} Baz ${Qux \n Quux} \n Corge';
        $r = iterator_to_array( Segment::coalesce( $parser->parse( $st ) ) );
        self::assertCount( 5, $r );

        self::assertSegment( SegmentType::LITERAL, 'Foo ', 'Foo ', $r[ 0 ] );
        self::assertSegment( SegmentType::WEAK_CALLBACK, 'Bar', '${Bar}', $r[ 1 ] );
        self::assertSegment( SegmentType::LITERAL, ' Baz ', ' Baz ', $r[ 2 ] );
        self::assertSegment( SegmentType::WEAK_CALLBACK, 'Qux \n Quux', '${Qux \n Quux}', $r[ 3 ] );
        self::assertSegment( SegmentType::LITERAL, " \n Corge", ' \n Corge', $r[ 4 ] );
    }


    private function makeComplexParser() : Parser {
        $comment = QuoteOperator::cComment();
        $hardQuote = QuoteOperator::single();
        $softQuote = QuoteOperator::double();
        $strongCallback = QuoteOperator::backtick();
        $weakCallback = QuoteOperator::varCurly();
        $openCallback = OpenEndedOperator::var();
        $escape = new MultiOperator( [
            new HexEscape(),
            new ControlCharEscape(),
        ] );
        $delimiter = DelimiterOperator::whitespace();
        $fnBacktick = function ( string $i_stValue ) : string {
            return strtoupper( $i_stValue );
        };
        $fnWeak = new Variables( [ 'foo' => 'bar' ] );
        $fnOpen = new Variables( [ 'baz' => 'qux' ] );
        return new Parser(
            comment: $comment,
            hardQuote: $hardQuote,
            softQuote: $softQuote,
            strongCallback: $strongCallback,
            weakCallback: $weakCallback,
            openCallback: $openCallback,
            escape: $escape,
            delimiter: $delimiter,
            fnStrong: $fnBacktick,
            fnWeak: $fnWeak,
            fnOpen: $fnOpen
        );
    }


}
