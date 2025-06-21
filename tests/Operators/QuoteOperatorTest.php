<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Exception;
use JDWX\Quote\Operators\QuoteOperator;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


#[CoversClass( QuoteOperator::class )]
final class QuoteOperatorTest extends OperatorTestCase {


    public function testBacktick() : void {
        $quote = QuoteOperator::backtick();
        self::assertSame( 'Foo', $quote( '`Foo`' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo`Bar`Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testComment() : void {
        $quote = QuoteOperator::comment();
        self::assertSame( 'Foo', $quote( '/*Foo*/' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo/*Bar*/Baz' ) );
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
        self::assertNull( $quote->match( 'Foo' ) );
        self::assertNull( $quote->match( "'Foo" ) );

        self::assertPiece( "'Foo'", 'Foo', '', $quote->match( "'Foo'" ) );
        self::assertPiece( "'Foo\'Bar'", "Foo'Bar", '', $quote->match( "'Foo\'Bar'" ) );

        $quote = QuoteOperator::single();
        self::expectException( Exception::class );
        $quote->match( "'Foo" );
    }


    public function testMatchForCustomQuotes() : void {
        $quote = new QuoteOperator( '<<<', '>>>' );
        self::assertNull( $quote->match( 'Foo' ) );
        self::assertPiece( '<<<Foo>>>', 'Foo', 'Bar', $quote->match( '<<<Foo>>>Bar' ) );
    }


    public function testSimple() : void {
        $quote = QuoteOperator::simple();
        self::assertSame( 'Foo', $quote( 'Foo' ) );
        self::assertSame( 'FooBarBaz', $quote( "Foo'Bar'Baz" ) );
        self::assertSame( 'FooBarBaz', $quote( '"Foo"Bar"Baz"' ) );
        self::assertSame( 'Foo`Bar`Baz', $quote( 'Foo`Bar`Baz' ) );
    }


    public function testVarCurly() : void {
        $quote = QuoteOperator::varCurly();
        self::assertSame( 'Foo', $quote( '${Foo}' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo${Bar}Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


    public function testVarParen() : void {
        $quote = QuoteOperator::varParen();
        self::assertSame( 'Foo', $quote( '$(Foo)' ) );
        self::assertSame( 'FooBarBaz', $quote( 'Foo$(Bar)Baz' ) );
        self::assertSame( "Foo'Bar'Baz", $quote( "Foo'Bar'Baz" ) );
    }


}
