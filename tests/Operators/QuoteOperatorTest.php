<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Exception;
use JDWX\Quote\Operators\QuoteOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( QuoteOperator::class )]
final class QuoteOperatorTest extends TestCase {


    public function testBacktick() : void {
        $quote = QuoteOperator::backtick();
        self::assertSame( 'Foo', $quote( '`Foo`' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo`Bar`Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testDouble() : void {
        $quote = QuoteOperator::double();
        self::assertSame( 'Foo', $quote( '"Foo"' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo"Bar"Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testInvoke() : void {
        $quote = QuoteOperator::single();
        self::assertSame( 'Foo', $quote( 'Foo' ) );
        self::assertSame( 'FooBarBaz', $quote( "Foo'Bar'Baz" ) );
    }


    public function testInvokeForCustomQuotes() : void {
        $quote = new QuoteOperator( '<<<', '>>>' );
        self::assertSame( 'Foo', $quote( '<<<Foo>>>' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo<<<Bar>>>Baz' ) );
        self::assertSame( 'FooBarBaz', $quote( '<<<Foo>>>Bar<<<Baz>>>' ) );
    }


    public function testMatch() : void {
        $quote = QuoteOperator::single( i_bIgnoreUnclosed: true );
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

        $quote = QuoteOperator::single();
        self::expectException( Exception::class );
        $quote->match( "'Foo" );
    }


    public function testMatchForCustomQuotes() : void {
        $quote = new QuoteOperator( '<<<', '>>>' );
        $match = $quote->match( '<<<Foo>>>Bar' );
        self::assertSame( '<<<Foo>>>', $match->stMatch );
        self::assertSame( 'Foo', $match->stReplace );
        self::assertSame( 'Bar', $match->stRest );
    }


}
