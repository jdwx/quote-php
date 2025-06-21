<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Segment;
use JDWX\Quote\SegmentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Segment::class )]
final class SegmentTest extends TestCase {


    public function testAppend() : void {
        $s = new Segment( SegmentType::LITERAL, 'Foo', 'Bar' );
        $s = $s->append( 'Baz', 'Qux' );
        self::assertSame( SegmentType::LITERAL, $s->type );
        self::assertSame( 'FooBaz', $s->value );
        self::assertSame( 'BarQux', $s->original );
    }


    public function testCoalesce() : void {
        $r = [
            new Segment( SegmentType::LITERAL, 'Foo', 'Foo' ),
            new Segment( SegmentType::LITERAL, 'Bar', 'Bar' ),
            new Segment( SegmentType::DELIMITER, 'Baz', 'Baz' ),
            new Segment( SegmentType::DELIMITER, 'Qux', 'Qux' ),
            new Segment( SegmentType::SOFT_QUOTED, 'Grault', 'Grault' ),
            new Segment( SegmentType::SOFT_QUOTED, 'Garply', 'Garply' ),
            new Segment( SegmentType::UNDEFINED, 'Quux', 'Quux' ),
            new Segment( SegmentType::UNDEFINED, 'Corge', 'Corge' ),
        ];
        $r = iterator_to_array( Segment::coalesce( $r ) );

        // self::assertCount( 5, $r );
        $segment = $r[ 0 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::LITERAL, $segment->type );
        self::assertSame( 'FooBar', $segment->value );
        self::assertSame( 'FooBar', $segment->original );

        $segment = $r[ 1 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::DELIMITER, $segment->type );
        self::assertSame( 'BazQux', $segment->value );
        self::assertSame( 'BazQux', $segment->original );

        $segment = $r[ 2 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::SOFT_QUOTED, $segment->type );
        self::assertSame( 'Grault', $segment->value );
        self::assertSame( 'Grault', $segment->original );

        $segment = $r[ 3 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::SOFT_QUOTED, $segment->type );
        self::assertSame( 'Garply', $segment->value );
        self::assertSame( 'Garply', $segment->original );

        $segment = $r[ 4 ];
        assert( $segment instanceof Segment );
        self::assertSame( SegmentType::UNDEFINED, $segment->type );
        self::assertSame( 'QuuxCorge', $segment->value );
        self::assertSame( 'QuuxCorge', $segment->original );
    }


    public function testDropDelimiters() : void {
        $s1 = new Segment( SegmentType::LITERAL, 'Foo', 'Foo' );
        $s2 = new Segment( SegmentType::DELIMITER, 'Bar', 'Bar' );
        $s3 = new Segment( SegmentType::LITERAL, 'Baz', 'Baz' );
        $s4 = new Segment( SegmentType::DELIMITER, 'Qux', 'Qux' );
        $s5 = new Segment( SegmentType::SOFT_QUOTED, 'Grault', 'Grault' );
        $r = [ $s1, $s2, $s3, $s4, $s5 ];
        $r = iterator_to_array( Segment::dropDelimiters( $r ) );
        self::assertCount( 3, $r );
        self::assertSame( [ $s1, $s3, $s5 ], $r );
    }


    public function testMerge() : void {
        $s1 = new Segment( SegmentType::LITERAL, 'Foo', 'Bar' );
        $s2 = new Segment( SegmentType::LITERAL, 'Baz', 'Qux' );
        $s = $s1->merge( $s2 );
        self::assertSame( SegmentType::LITERAL, $s->type );
        self::assertSame( 'FooBaz', $s->value );
        self::assertSame( 'BarQux', $s->original );

        $s3 = new Segment( SegmentType::DELIMITER, 'Quux', 'Corge' );
        self::expectException( \InvalidArgumentException::class );
        $s1->merge( $s3 );
    }


    public function testMergeOriginal() : void {
        $s1 = new Segment( SegmentType::LITERAL, 'Foo', 'Bar' );
        $s2 = new Segment( SegmentType::LITERAL, 'Baz', 'Qux' );
        $s3 = new Segment( SegmentType::DELIMITER, 'Quux', 'Corge' );
        $s4 = new Segment( SegmentType::SOFT_QUOTED, 'Grault', 'Garply' );

        $r = Segment::mergeOriginal( [ $s1, $s2, $s3, $s4 ] );
        self::assertSame( 'BarQuxCorgeGarply', $r );
    }


    public function testMergeValues() : void {
        $s1 = new Segment( SegmentType::LITERAL, 'Foo', 'Bar' );
        $s2 = new Segment( SegmentType::LITERAL, 'Baz', 'Qux' );
        $s3 = new Segment( SegmentType::DELIMITER, 'Quux', 'Corge' );
        $s4 = new Segment( SegmentType::SOFT_QUOTED, 'Grault', 'Garply' );

        $r = Segment::mergeValues( [ $s1, $s2, $s3, $s4 ] );
        self::assertSame( 'FooBazQuuxGrault', $r );
    }


}
