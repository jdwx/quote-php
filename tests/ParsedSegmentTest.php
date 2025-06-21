<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\ParsedSegment;
use JDWX\Quote\SegmentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ParsedSegment::class )]
final class ParsedSegmentTest extends TestCase {


    public function testDebug() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $r = $x->debug();
        self::assertCount( 3, $r );
        self::assertSame( SegmentType::UNQUOTED, $r[ 'type' ] );
        self::assertSame( 'foo', $r[ 'textOriginal' ] );
        self::assertSame( 'foo', $r[ 'textProcessed' ] );
    }


    public function testGetOriginal() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getOriginal() );
        $x = new ParsedSegment( SegmentType::HARD_QUOTED, 'foo' );
        self::assertSame( "'foo'", $x->getOriginal() );
        $x = new ParsedSegment( SegmentType::SOFT_QUOTED, 'foo' );
        self::assertSame( '"foo"', $x->getOriginal() );
        $x = new ParsedSegment( SegmentType::STRONG_CALLBACK, 'foo' );
        self::assertSame( '`foo`', $x->getOriginal() );
        $x = new ParsedSegment( SegmentType::COMMENT, 'foo' );
        self::assertSame( '', $x->getOriginal() );
    }


    public function testGetProcessed() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( SegmentType::HARD_QUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( SegmentType::SOFT_QUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( SegmentType::STRONG_CALLBACK, 'foo' );
        self::assertSame( 'foo', $x->getProcessed() );
        $x = new ParsedSegment( SegmentType::COMMENT, 'foo' );
        self::assertSame( '', $x->getProcessed() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $x->getProcessed( true ) );
        $x = new ParsedSegment( SegmentType::HARD_QUOTED, 'foo' );
        self::assertSame( "'foo'", $x->getProcessed( true ) );
        $x = new ParsedSegment( SegmentType::SOFT_QUOTED, 'foo' );
        self::assertSame( '"foo"', $x->getProcessed( true ) );
        $x = new ParsedSegment( SegmentType::STRONG_CALLBACK, 'foo' );
        self::assertSame( '`foo`', $x->getProcessed( true ) );
        $x = new ParsedSegment( SegmentType::COMMENT, 'foo' );
        self::assertSame( '', $x->getProcessed( true ) );

    }


    public function testIsComment() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertFalse( $x->isComment() );
        $x = new ParsedSegment( SegmentType::COMMENT, 'foo' );
        self::assertTrue( $x->isComment() );
    }


    public function testIsDelimiter() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertFalse( $x->isDelimiter() );
        $x = new ParsedSegment( SegmentType::DELIMITER, 'foo' );
        self::assertTrue( $x->isDelimiter() );
    }


    public function testSubstBackQuotes() : void {
        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $x->substBackQuotes( function ( $i_st ) {
            return $i_st;
        } );
        self::assertSame( 'foo', $x->getProcessed() );

        $x = new ParsedSegment( SegmentType::STRONG_CALLBACK, 'foo' );
        $x->substBackQuotes( function () {
            return 'bar';
        } );
        self::assertSame( 'bar', $x->getProcessed() );
    }


    public function testSubstEscapeSequences() : void {
        $seg = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        self::assertSame( 'foo', $seg->substEscapeSequences( 'foo' ) );
        self::assertSame( "FooBar\n", $seg->substEscapeSequences( 'F\o\o\102\U0061r\n' ) );
    }


    public function testSubstVariablesForBare() : void {
        $rVariables = [ 'bar' => 'qux' ];

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $y = $x->substVariables( [] );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \$bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo qux baz', $x->getProcessed() );
        self::assertSame( "foo \$bar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareValidAfterError() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \$baz \$bar" );
        $y = $x->substVariables( $rVariables );
        # It's still an error.
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBareWithMultipleLonger() : void {
        $rVariables = [ 'foo' => 'qux', 'foobar' => 'quux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \$foobar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo quux baz', $x->getProcessed() );
        self::assertSame( "foo \$foobar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareWithMultipleShorter() : void {
        $rVariables = [ 'foobar' => 'quux', 'foo' => 'qux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \$foobar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo quux baz', $x->getProcessed() );
        self::assertSame( "foo \$foobar baz", $x->getOriginal() );
    }


    public function testSubstVariablesForBareWithUndefinedVariable() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \$baz" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBraces() : void {
        $rVariables = [ 'bar' => 'qux' ];

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $y = $x->substVariables( [] );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo', $x->getProcessed() );
        self::assertSame( 'foo', $x->getOriginal() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \${bar} baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo qux baz', $x->getProcessed() );
        self::assertSame( "foo \${bar} baz", $x->getOriginal() );

        $x = new ParsedSegment( SegmentType::UNQUOTED, 'foo {bar} baz' );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( 'foo {bar} baz', $x->getProcessed() );
        self::assertSame( 'foo {bar} baz', $x->getOriginal() );

    }


    public function testSubstVariablesForBracesWithUndefinedVariable() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \${baz}" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


    public function testSubstVariablesForBracesWithUnmatchedBrace() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( SegmentType::UNQUOTED, "foo \${bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Unmatched', strval( $y ) );
    }


    public function testSubstVariablesForSingleQuotes() : void {
        $rVariables = [ 'bar' => 'qux' ];
        $x = new ParsedSegment( SegmentType::HARD_QUOTED, "foo \$bar baz" );
        $y = $x->substVariables( $rVariables );
        self::assertTrue( $y );
        self::assertSame( "foo \$bar baz", $x->getProcessed() );
        self::assertSame( "'foo \$bar baz'", $x->getOriginal() );
    }


}
