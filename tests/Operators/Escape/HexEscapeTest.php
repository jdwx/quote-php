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
        self::assertPiece( '\x41', 'A', '', $hex->match( '\x41' ) );
        self::assertPiece( '\x42', 'B', '!', $hex->match( '\x42!' ) );
    }


}
