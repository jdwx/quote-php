<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\OctalEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( OctalEscape::class )]
final class OctalEscapeTest extends OperatorTestCase {


    public function testInvoke() : void {
        $octal = new OctalEscape();
        self::assertSame( 'Foo', $octal( 'Foo' ) );
        self::assertSame( 'FooBar', $octal( 'Foo\102\141r' ) );
        self::assertSame( 'FooBar\n', $octal( 'Foo\102\141r\n' ) );
    }


    public function testMatch() : void {
        $octal = new OctalEscape();
        self::assertNull( $octal->match( 'Foo' ) );
        self::assertPiece( '\141', 'a', '', $octal->match( '\141' ) );
        self::assertPiece( '\102', 'B', '!', $octal->match( '\102!' ) );
    }


}
