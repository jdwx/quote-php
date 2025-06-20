<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\HexEscape;
use JDWX\Quote\Escape\OctalEscape;
use JDWX\Quote\MultiOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( MultiOperator::class )]
final class MultiEscapeTest extends TestCase {


    public function testInvoke() : void {
        $escapes = new MultiOperator( new HexEscape() );
        self::assertSame( 'FooB\141r\n', $escapes( 'Foo\x42\141r\n' ) );

        $escapes = new MultiOperator( [
            new HexEscape(),
            new OctalEscape(),
        ] );
        self::assertSame( 'FooBar\n', $escapes( 'Foo\x42\141r\n' ) );

        # Test that the escapes don't stack on a single call.
        $st = $escapes( 'FooB\x5c141r' );
        self::assertSame( 'FooB\\141r', $st );
        $st = $escapes( $st );
        self::assertSame( 'FooBar', $st );

    }


    public function testMatch() : void {
        $escapes = new MultiOperator( [
            new HexEscape(),
            new OctalEscape(),
        ] );
        self::assertNull( $escapes->match( 'Foo' ) );

        $match = $escapes->match( '\x42Foo' );
        self::assertSame( '\x42', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );
    }


}
