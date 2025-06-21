<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\HexEscape;
use JDWX\Quote\Operators\Escape\OctalEscape;
use JDWX\Quote\Operators\MultiOperator;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( MultiOperator::class )]
final class MultiOperatorTest extends OperatorTestCase {


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
        self::assertPiece( '\x42', 'B', 'Foo', $escapes->match( '\x42Foo' ) );
    }


}
