<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\ControlCharEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( ControlCharEscape::class )]
final class ControlCharEscapeTest extends OperatorTestCase {


    public function testInvoke() : void {
        $control = new ControlCharEscape();
        self::assertSame( 'Foo', $control( 'Foo' ) );
        self::assertSame( "Foo\rBar", $control( 'Foo\rBar' ) );
        self::assertSame( "Foo\nBar", $control( 'Foo\nBar' ) );
        self::assertSame( "Hello\tWorld", $control( 'Hello\tWorld' ) );
        self::assertSame( "Hello\vWorld", $control( 'Hello\vWorld' ) );
        self::assertSame( "Hello\fWorld", $control( 'Hello\fWorld' ) );
        self::assertSame( "Hello\0World", $control( 'Hello\0World' ) );
        self::assertSame( 'Hello\aWorld', $control( 'Hello\aWorld' ) );
        self::assertSame( 'Hello\bWorld', $control( 'Hello\bWorld' ) );
    }


    public function testMatch() : void {
        $control = new ControlCharEscape();
        self::assertNull( $control->match( 'Foo' ) );
        self::assertPiece( '\r', "\r", '', $control->match( '\r' ) );
        self::assertPiece( '\n', "\n", '', $control->match( '\n' ) );
        self::assertPiece( '\t', "\t", 'Hello', $control->match( '\tHello' ) );
        self::assertPiece( '\v', "\v", 'Foo', $control->match( '\vFoo' ) );
        self::assertPiece( '\f', "\f", 'Bar', $control->match( '\fBar' ) );
        self::assertPiece( '\0', "\0", 'Baz', $control->match( '\0Baz' ) );
        self::assertPiece( '\a', '\a', '', $control->match( '\a' ) );
        self::assertPiece( '\b', '\b', '', $control->match( '\b' ) );
    }


}
