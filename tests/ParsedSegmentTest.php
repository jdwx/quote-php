<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\ParsedSegment;
use JDWX\Quote\Segment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ParsedSegment::class )]
final class ParsedSegmentTest extends TestCase {


    public function testDebug() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $r = $x->debug();
        self::assertCount( 3, $r );
        self::assertSame( Segment::UNQUOTED, $r[ 'type' ] );
        self::assertSame( 'foo', $r[ 'textOriginal' ] );
        self::assertSame( 'foo', $r[ 'textProcessed' ] );
    }


    public function testGetOriginal() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getOriginal() );
        $x = new ParsedSegment( Segment::SINGLE_QUOTED, 'foo' );
        self::assertSame( "'foo'", $x->getOriginal() );
        $x = new ParsedSegment( Segment::DOUBLE_QUOTED, 'foo' );
        self::assertSame( '"foo"', $x->getOriginal() );
        $x = new ParsedSegment( Segment::BACK_QUOTED, 'foo' );
        self::assertSame( '`foo`', $x->getOriginal() );
        $x = new ParsedSegment( Segment::COMMENT, 'foo' );
        self::assertSame( '', $x->getOriginal() );
    }


    public function testGetProcessed() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( Segment::SINGLE_QUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( Segment::DOUBLE_QUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( Segment::BACK_QUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( Segment::COMMENT, 'foo' );
        self::assertSame( '', $x->getProcessed() );

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed( true ) );
        $x = new ParsedSegment( Segment::SINGLE_QUOTED, 'foo' );
        self::assertSame( "'foo'", $x->getProcessed( true ) );
        $x = new ParsedSegment( Segment::DOUBLE_QUOTED, 'foo' );
        self::assertSame( '"foo"', $x->getProcessed( true ) );
        $x = new ParsedSegment( Segment::BACK_QUOTED, 'foo' );
        self::assertSame( '`foo`', $x->getProcessed( true ) );
        $x = new ParsedSegment( Segment::COMMENT, 'foo' );
        self::assertSame( '', $x->getProcessed( true ) );

    }


    public function testIsComment() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        self::assertFalse( $x->isComment() );
        $x = new ParsedSegment( Segment::COMMENT, 'foo' );
        self::assertTrue( $x->isComment() );
    }


    public function testIsDelimiter() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        self::assertFalse( $x->isDelimiter() );
        $x = new ParsedSegment( Segment::DELIMITER, 'foo' );
        self::assertTrue( $x->isDelimiter() );
    }


    public function testSubstBackQuotes() : void {
        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $x->substBackQuotes( function ( $i_st ) {
            return $i_st;
        } );
        self::assertSame( 'foo', $x->getProcessed() );

        $x = new ParsedSegment( Segment::BACK_QUOTED, 'foo' );
        $x->substBackQuotes( function () {
            return 'bar';
        } );
        self::assertSame( 'bar', $x->getProcessed() );
    }


    public function testSubstEscapeSequences() : void {
        self::assertSame( 'foo', ParsedSegment::substEscapeSequences( 'foo' ) );
        self::assertSame( "Foo\nBar", ParsedSegment::substEscapeSequences( 'Foo\nBar' ) );
        self::assertSame( "Foo\rBar", ParsedSegment::substEscapeSequences( 'Foo\rBar' ) );
        self::assertSame( "Foo\tBar", ParsedSegment::substEscapeSequences( 'Foo\tBar' ) );
        self::assertSame( "Foo\vBar", ParsedSegment::substEscapeSequences( 'Foo\vBar' ) );
        self::assertSame( "Foo\eBar", ParsedSegment::substEscapeSequences( 'Foo\eBar' ) );
        self::assertSame( "Foo\fBar", ParsedSegment::substEscapeSequences( 'Foo\fBar' ) );
        self::assertSame( "Foo\0Bar", ParsedSegment::substEscapeSequences( 'Foo\0Bar' ) );
        self::assertSame( 'Foo aBar', ParsedSegment::substEscapeSequences( 'Foo \aBar' ) );
        self::assertSame( 'Foo bBar', ParsedSegment::substEscapeSequences( 'Foo \bBar' ) );

        self::assertSame( 'FooBar', ParsedSegment::substEscapeSequences( 'Foo\\Bar' ) );

        # Octal test.
        self::assertSame( 'FooBar', ParsedSegment::substEscapeSequences( 'FooB\141r' ) );

        # Unicode test.
        self::assertSame( 'FooBar', ParsedSegment::substEscapeSequences( 'FooB\u0061r' ) );
        self::assertSame( 'FooBar', ParsedSegment::substEscapeSequences( 'Foo\U0042ar' ) );

        self::assertSame( 'Foo\\Bar', ParsedSegment::substEscapeSequences( 'Foo\\\\Bar' ) );
    }


    public function testSubstVariablesForBare() : void {
        $rVariables = [ 'bar' => 'qux' ];

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $y = $x->substVariables( [] );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( Segment::UNQUOTED, "foo \$bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo qux baz', $x->getProcessed() );
        self::assertSame( "foo \$bar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareValidAfterError() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \$baz \$bar" );
        $y = $x->substVariables( $rVariables );
        # It's still an error.
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBareWithMultipleLonger() : void {
        $rVariables = [ 'foo' => 'qux', 'foobar' => 'quux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \$foobar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo quux baz', $x->getProcessed() );
        self::assertSame( "foo \$foobar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareWithMultipleShorter() : void {
        $rVariables = [ 'foobar' => 'quux', 'foo' => 'qux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \$foobar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo quux baz', $x->getProcessed() );
        self::assertSame( "foo \$foobar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareWithUndefinedVariable() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \$baz" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBraces() : void {
        $rVariables = [ 'bar' => 'qux' ];

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $y = $x->substVariables( [] );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( Segment::UNQUOTED, "foo \${bar} baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo qux baz', $x->getProcessed() );
        self::assertSame( "foo \${bar} baz", $x->getOriginal() );

        $x = new ParsedSegment( Segment::UNQUOTED, 'foo {bar} baz' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo {bar} baz', $x->getProcessed() );
        self::assertSame( 'foo {bar} baz', $x->getOriginal() );

    }


    public function testSubstVariablesForBracesWithUndefinedVariable() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \${baz}" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBracesWithUnmatchedBrace() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( Segment::UNQUOTED, "foo \${bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Unmatched', strval( $y ) );
    }


    public function testSubstVariablesForSingleQuotes() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( Segment::SINGLE_QUOTED, "foo \$bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( "foo \$bar baz", $x->getProcessed() );
        self::assertSame( "'foo \$bar baz'", $x->getOriginal() );
    }


}
