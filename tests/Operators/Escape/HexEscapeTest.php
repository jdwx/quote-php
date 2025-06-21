<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\HexEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( HexEscape::class )]
final class HexEscapeTest extends OperatorTestCase {


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
