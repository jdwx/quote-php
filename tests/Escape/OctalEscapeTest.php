<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\OctalEscape;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( OctalEscape::class )]
final class OctalEscapeTest extends TestCase {


    public function testInvoke() : void {
        $octal = new OctalEscape();
        self::assertSame( 'Foo', $octal( 'Foo' ) );
        self::assertSame( 'FooBar', $octal( 'Foo\102\141r' ) );
        self::assertSame( 'FooBar\n', $octal( 'Foo\102\141r\n' ) );
    }


    public function testMatch() : void {
        $octal = new OctalEscape();
        self::assertNull( $octal->match( 'Foo' ) );

        $match = $octal->match( '\141' );
        self::assertSame( '\141', $match->stMatch );
        self::assertSame( 'a', $match->stReplace );
        self::assertSame( '', $match->stRest );

        $match = $octal->match( '\102!' );
        self::assertSame( '\102', $match->stMatch );
        self::assertSame( 'B', $match->stReplace );
        self::assertSame( '!', $match->stRest );
    }


}
