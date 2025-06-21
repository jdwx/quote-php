<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\Unicode2BEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( Unicode2BEscape::class )]
final class Unicode2BEscapeTest extends OperatorTestCase {


    public function testInvoke() : void {
        $unicode = new Unicode2BEscape();
        self::assertSame( 'Foo', $unicode( 'Foo' ) );
        self::assertSame( 'FooBar', $unicode( 'Foo\u0042ar' ) );
        self::assertSame( 'FooBar', $unicode( 'Foo\u0042\u0061r' ) );
    }


    public function testMatch() : void {
        $unicode = new Unicode2BEscape();
        self::assertNull( $unicode->match( 'Foo' ) );

        self::assertPiece( '\u0042', 'B', '', $unicode->match( '\u0042' ) );
        self::assertPiece( '\U0042', 'B', 'ar', $unicode->match( '\U0042ar' ) );
    }


}
