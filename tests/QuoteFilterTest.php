<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Exception;
use JDWX\Quote\QuoteFilter;
use JDWX\Quote\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( QuoteFilter::class )]
final class QuoteFilterTest extends TestCase {


    public function testBacktick() : void {
        $quote = QuoteFilter::backtick();
        self::assertSame( 'Foo', $quote( '`Foo`' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo`Bar`Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testDouble() : void {
        $quote = QuoteFilter::double();
        self::assertSame( 'Foo', $quote( '"Foo"' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo"Bar"Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testInvoke() : void {
        $quote = QuoteFilter::single();
        self::assertSame( 'Foo', $quote( 'Foo' ) );
        self::assertSame( 'FooBarBaz', $quote( "Foo'Bar'Baz" ) );
    }


    public function testInvokeForCustomQuotes() : void {
        $quote = new QuoteFilter( Segment::HARD_QUOTED, '<<<', '>>>' );
        self::assertSame( 'Foo', $quote( '<<<Foo>>>' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo<<<Bar>>>Baz' ) );
        self::assertSame( 'FooBarBaz', $quote( '<<<Foo>>>Bar<<<Baz>>>' ) );
    }


    public function testMatch() : void {
        $quote = QuoteFilter::single( i_bIgnoreUnclosed: true );
        $match = $quote->match( 'Foo' );
        self::assertNull( $match );

        $match = $quote->match( '"Foo"' );
        self::assertNull( $match );

        $match = $quote->match( "'Foo'" );
        self::assertSame( "'Foo'", $match->stMatch );
        self::assertSame( 'Foo', $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $quote->match( "'Foo\'Bar'" );
        self::assertSame( "'Foo\'Bar'", $match->stMatch );
        self::assertSame( "Foo'Bar", $match->stReplace );
        self::assertSame( '', $match->stRest );

        self::assertNull( $quote->match( "'Foo" ) );

        $quote = QuoteFilter::single();
        self::expectException( Exception::class );
        $quote->match( "'Foo" );
    }


    public function testMatchForCustomQuotes() : void {
        $quote = new QuoteFilter( Segment::HARD_QUOTED, '<<<', '>>>' );
        $match = $quote->match( '<<<Foo>>>Bar' );
        self::assertSame( '<<<Foo>>>', $match->stMatch );
        self::assertSame( 'Foo', $match->stReplace );
        self::assertSame( 'Bar', $match->stRest );
    }


}
