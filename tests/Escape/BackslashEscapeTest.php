<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\BackslashEscape;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( BackslashEscape::class )]
final class BackslashEscapeTest extends TestCase {


    public function testInvoke() : void {
        $backslash = new BackslashEscape();
        self::assertSame( 'Foo', $backslash( 'Foo' ) );
        self::assertSame( 'FooBar', $backslash( '\F\oo\B\ar' ) );
        self::assertSame( 'FooBar', $backslash( 'Foo\\Bar' ) );
        self::assertSame( 'Foo\\Bar', $backslash( 'Foo\\\\Bar' ) );
    }


    public function testMatch() : void {
        $backslash = new BackslashEscape();
        self::assertNull( $backslash->match( 'Foo' ) );

        $match = $backslash->match( '\Bar' );
        self::assertSame( '\\B', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( 'ar', $match->stRest );

        $match = $backslash->match( '\\\\Baz!' );
        self::assertSame( '\\\\', $match->stMatch );
        self::assertSame( '\\', $match->stReplace );
        self::assertSame( 'Baz!', $match->stRest );
    }


}
