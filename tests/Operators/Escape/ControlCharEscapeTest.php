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

        $match = $control->match( '\r' );
        self::assertSame( '\r', $match->stMatch );
        self::assertSame( "\r", $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $control->match( '\n' );
        self::assertSame( '\n', $match->stMatch );
        self::assertSame( "\n", $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $control->match( '\tHello' );
        self::assertSame( '\t', $match->stMatch );
        self::assertSame( "\t", $match->stReplace );
        self::assertSame( 'Hello', $match->stRest );

        $match = $control->match( '\vFoo' );
        self::assertSame( '\v', $match->stMatch );
        self::assertSame( "\v", $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );

        $match = $control->match( '\fBar' );
        self::assertSame( '\f', $match->stMatch );
        self::assertSame( "\f", $match->stReplace );
        self::assertSame( 'Bar', $match->stRest );

        $match = $control->match( '\0Baz' );
        self::assertSame( '\0', $match->stMatch );
        self::assertSame( "\0", $match->stReplace );
        self::assertSame( 'Baz', $match->stRest );

        $match = $control->match( '\a' );
        self::assertSame( '\a', $match->stMatch );
        self::assertSame( '\a', $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $control->match( '\b' );
        self::assertSame( '\b', $match->stMatch );
        self::assertSame( '\b', $match->stReplace );
        self::assertSame( '', $match->stRest );
    }


}
