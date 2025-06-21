<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\BackslashEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( BackslashEscape::class )]
final class BackslashEscapeTest extends OperatorTestCase {


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
        self::assertPiece( '\B', 'B', 'ar', $match );

        $match = $backslash->match( '\\\\Baz!' );
        self::assertPiece( '\\\\', '\\', 'Baz!', $match );
    }


}
