<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Delimiter;
use JDWX\Quote\Parser;
use JDWX\Quote\QuoteFilter;
use JDWX\Quote\Segment;
use PHPUnit\Framework\TestCase;


class ParserTest extends TestCase {


    public function testInvoke() : void {
        $parser = new Parser( [ QuoteFilter::double(), new Delimiter() ] );
        $r = $parser( 'Foo' );
        self::assertSame( 1, count( $r ) );
        self::assertSame( 'Foo', $r[ 0 ]->stMatch );
        self::assertSame( 'Foo', $r[ 0 ]->stReplace );
        self::assertSame( '', $r[ 0 ]->stRest );
        self::assertSame( Segment::UNQUOTED, $r[ 0 ]->segment );

        $r = $parser( 'Foo  "Bar Baz"' );
        self::assertSame( 3, count( $r ) );
        self::assertSame( 'Foo', $r[ 0 ]->stMatch );
        self::assertSame( 'Foo', $r[ 0 ]->stReplace );
        self::assertSame( '  "Bar Baz"', $r[ 0 ]->stRest );
        self::assertSame( Segment::UNQUOTED, $r[ 0 ]->segment );

        self::assertSame( '  ', $r[ 1 ]->stMatch );
        self::assertSame( '  ', $r[ 1 ]->stReplace );
        self::assertSame( '"Bar Baz"', $r[ 1 ]->stRest );
        self::assertSame( Segment::DELIMITER, $r[ 1 ]->segment );

        self::assertSame( '"Bar Baz"', $r[ 2 ]->stMatch );
        self::assertSame( 'Bar Baz', $r[ 2 ]->stReplace );
        self::assertSame( '', $r[ 2 ]->stRest );
        self::assertSame( Segment::SOFT_QUOTED, $r[ 2 ]->segment );
    }


    public function testInvokeForComplexParser() : void {
        $parser = new Parser( [
            QuoteFilter::single(),
            QuoteFilter::double(),
            QuoteFilter::backtick(),
            new Delimiter(),
        ] );

        $r = $parser( 'Foo "Bar `Baz` Qux" \'Quux `Corge` "Grault" Garply\'' );
        self::assertCount( 5, $r );
        var_dump( $r );
    }


}
