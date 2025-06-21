<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators\Escape;


use JDWX\Quote\Operators\AbstractOperator;
use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Operators\Escape\Unicode4BEscape;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../../Helpers/OperatorTestCase.php';


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( Unicode4BEscape::class )]
final class Unicode4BEscapeTest extends OperatorTestCase {


    public function testInvoke() : void {
        $unicode = new Unicode4BEscape();
        self::assertSame( '😀', $unicode( '\U+1F600' ) );
        self::assertSame( 'Foo😀', $unicode( 'Foo\U+1F600' ) );
        self::assertSame( '😀Qux', $unicode( '\U+1f600Qux' ) );
        // self::assertSame( 'Bar😀Baz', $unicode( 'Bar\U+0001f600Baz' ) );
        self::assertSame( 'Foo😀', $unicode( 'Foo\U{1F600}' ) );
        self::assertSame( '😀Bar', $unicode( '\U{1f600}Bar' ) );
        self::assertSame( 'Bar😀Qux', $unicode( 'Bar\U{0001f600}Qux' ) );
    }


    public function testMatch() : void {
        $unicode = new Unicode4BEscape();
        self::assertNull( $unicode->match( 'Foo' ) );
        self::assertNull( $unicode->match( 'Foo\U+1F600' ) );
        self::assertPiece( '\U+1F600', '😀', '', $unicode->match( '\U+1F600' ) );
        self::assertPiece( '\U{1F600}', '😀', '', $unicode->match( '\U{1F600}' ) );
    }


}
