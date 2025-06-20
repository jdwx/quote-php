<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Escape;


use JDWX\Quote\AbstractOperator;
use JDWX\Quote\Escape\AbstractEscape;
use JDWX\Quote\Escape\Unicode4BEscape;
use JDWX\Quote\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractOperator::class )]
#[CoversClass( AbstractEscape::class )]
#[CoversClass( Unicode4BEscape::class )]
final class Unicode4BEscapeTest extends TestCase {


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

        $match = $unicode->match( '\U+1F600' );
        self::assertSame( '\U+1F600', $match->stMatch );
        self::assertSame( '😀', $match->stReplace );
        self::assertSame( '', $match->stRest );
        self::assertSame( Segment::UNDEFINED, $match->segment );

        $match = $unicode->match( '\U{1F600}' );
        self::assertSame( '\U{1F600}', $match->stMatch );
        self::assertSame( '😀', $match->stReplace );
        self::assertSame( '', $match->stRest );
        self::assertSame( Segment::UNDEFINED, $match->segment );

        self::assertNull( $unicode->match( 'Foo\U+1F600' ) );
    }


}
