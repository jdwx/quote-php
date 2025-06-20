<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\HexEscape;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( HexEscape::class )]
final class HexEscapeTest extends TestCase {


    public function testInvoke() : void {
        $hex = new HexEscape();
        self::assertSame( 'Foo', $hex( 'Foo' ) );
        self::assertSame( 'FooBar', $hex( 'Foo\x42\x61r' ) );
        self::assertSame( 'FooBar\n', $hex( 'Foo\x42\x61r\n' ) );
    }


    public function testMatch() : void {
        $hex = new HexEscape();
        self::assertNull( $hex->match( 'Foo' ) );
        $match = $hex->match( '\x41' );
        self::assertSame( '\x41', $match->stMatch );
        self::assertSame( 'A', $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $hex->match( '\x42!' );
        self::assertSame( '\x42', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( '!', $match->stRest );
    }


}
