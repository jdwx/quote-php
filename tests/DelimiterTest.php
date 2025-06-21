<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Operators\DelimiterOperator;
use PHPUnit\Framework\TestCase;


class DelimiterTest extends TestCase {


    public function testMatch() : void {
        $delim = new DelimiterOperator();
        self::assertNull( $delim->match( 'Foo' ) );

        $match = $delim->match( ' Foo' );
        self::assertSame( ' ', $match->stMatch );
        self::assertSame( ' ', $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );

        $match = $delim->match( "   \t  Foo" );
        self::assertSame( "   \t  ", $match->stMatch );
        self::assertSame( "   \t  ", $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );
    }


}
