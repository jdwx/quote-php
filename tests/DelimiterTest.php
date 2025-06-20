<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Delimiter;
use JDWX\Quote\Segment;
use PHPUnit\Framework\TestCase;


class DelimiterTest extends TestCase {


    public function testMatch() : void {
        $delim = new Delimiter();
        self::assertNull( $delim->match( 'Foo' ) );

        $match = $delim->match( ' Foo' );
        self::assertSame( ' ', $match->stMatch );
        self::assertSame( ' ', $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );
        self::assertSame( Segment::DELIMITER, $match->segment );

        $match = $delim->match( "   \t  Foo" );
        self::assertSame( "   \t  ", $match->stMatch );
        self::assertSame( "   \t  ", $match->stReplace );
        self::assertSame( 'Foo', $match->stRest );
        self::assertSame( Segment::DELIMITER, $match->segment );
    }


}
