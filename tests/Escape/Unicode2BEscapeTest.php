<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\Unicode2BEscape;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( Unicode2BEscape::class )]
final class Unicode2BEscapeTest extends TestCase {


    public function testInvoke() : void {
        $unicode = new Unicode2BEscape();
        self::assertSame( 'Foo', $unicode( 'Foo' ) );
        self::assertSame( 'FooBar', $unicode( 'Foo\u0042ar' ) );
        self::assertSame( 'FooBar', $unicode( 'Foo\u0042\u0061r' ) );
    }


    public function testMatch() : void {
        $unicode = new Unicode2BEscape();
        self::assertNull( $unicode->match( 'Foo' ) );

        $match = $unicode->match( '\u0042' );
        self::assertSame( '\u0042', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $unicode->match( '\U0042ar' );
        self::assertSame( '\U0042', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( 'ar', $match->stRest );
    }


}
