<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\ParsedString;
use JDWX\Quote\Segment;
use JDWX\Quote\StringParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ParsedString::class )]
final class ParsedStringTest extends TestCase {


    public function testAddBackQuoted() : void {
        $x = new ParsedString();
        $x->addBackQuoted( 'Foo' );
        self::assertCount( 1, $x );
        self::assertSame( '`Foo`', $x->getSegment( 0 )->getOriginal() );
    }


    public function testAddComment() : void {
        $x = new ParsedString();
        $x->addComment( 'Foo' );
        self::assertCount( 1, $x );
        self::assertSame( '', $x->getSegment( 0 )->getOriginal() );
    }


    public function testAddDoubleQuoted() : void {
        $x = new ParsedString();
        $x->addDoubleQuoted( 'Foo' );
        self::assertCount( 1, $x );
        self::assertSame( '"Foo"', $x->getSegment( 0 )->getOriginal() );
    }


    public function testAddSingleQuoted() : void {
        $x = new ParsedString();
        $x->addSingleQuoted( 'Foo' );
        self::assertCount( 1, $x );
        self::assertSame( "'Foo'", $x->getSegment( 0 )->getOriginal() );
    }


    public function testAddSpace() : void {
        $x = new ParsedString();
        $x->addSpace();
        self::assertCount( 1, $x );
        self::assertSame( ' ', $x->getSegment( 0 )->getOriginal() );
        self::assertTrue( $x->getSegment( 0 )->isDelimiter() );
    }


    public function testAddUnquoted() : void {
        $x = new ParsedString();
        $x->addUnquoted( 'Foo' );
        self::assertCount( 1, $x );
        self::assertSame( 'Foo', $x->getSegment( 0 )->getOriginal() );
    }


    public function testCount() : void {
        $x = new ParsedString();
        self::assertCount( 0, $x );
        $x->addUnquoted( 'foo' );
        self::assertCount( 1, $x );
        $x->addSpace();
        self::assertCount( 2, $x );
        $x->addDoubleQuoted( 'bar' );
        self::assertCount( 3, $x );
        $x->addSpace();
        self::assertCount( 4, $x );
        $x->addUnquoted( 'baz' );
        self::assertCount( 5, $x );
    }


    public function testDebug() : void {
        $x = StringParser::parseStringEx( "foo \"bar\" \$baz" );
        $x->substVariables( [ 'baz' => 'qux' ] );
        $r = $x->debug();
        self::assertCount( 5, $r );
        self::assertSame( Segment::UNQUOTED, $r[ 0 ][ 'type' ] );
        self::assertSame( Segment::DELIMITER, $r[ 1 ][ 'type' ] );
        self::assertSame( Segment::SOFT_QUOTED, $r[ 2 ][ 'type' ] );
        self::assertSame( Segment::DELIMITER, $r[ 3 ][ 'type' ] );
        self::assertSame( Segment::UNQUOTED, $r[ 4 ][ 'type' ] );
        self::assertSame( 'foo', $r[ 0 ][ 'textOriginal' ] );
        self::assertSame( 'foo', $r[ 0 ][ 'textProcessed' ] );
        self::assertSame( "\"bar\"", $r[ 2 ][ 'textOriginal' ] );
        self::assertSame( 'bar', $r[ 2 ][ 'textProcessed' ] );
        self::assertSame( "\$baz", $r[ 4 ][ 'textOriginal' ] );
        self::assertSame( 'qux', $r[ 4 ][ 'textProcessed' ] );
    }


    public function testGetOriginal() : void {
        $x = StringParser::parseStringEx( 'foo bar baz' );
        self::assertEquals( 'foo bar baz', $x->getOriginal() );
        self::assertEquals( 'bar baz', $x->getOriginal( 1 ) );
    }


    public function testGetProcessed() : void {
        $x = StringParser::parseStringEx( 'foo bar baz' );
        self::assertEquals( 'foo bar baz', $x->getProcessed() );

        $x = StringParser::parseStringEx( 'foo $bar baz' );
        $x->substVariables( [ 'bar' => 'qux' ] );
        self::assertEquals( 'foo qux baz', $x->getProcessed() );

        $x = StringParser::parseStringEx( 'foo "bar baz" qux' );
        self::assertEquals( 'foo bar baz qux', $x->getProcessed() );

    }


    public function testGetSegments() : void {
        $x = StringParser::parseStringEx( 'foo bar baz' );
        $r = $x->getSegments();
        self::assertEquals( 'foo', $r[ 0 ] );
        self::assertEquals( 'bar', $r[ 1 ] );
        self::assertEquals( 'baz', $r[ 2 ] );
        self::assertCount( 3, $r );
    }


    public function testSubstBackQuotes() : void {
        $x = StringParser::parseStringEx( 'baz foo qux' );
        $x->substBackQuotes( function ( $i_st ) {
            return $i_st;
        } );
        self::assertSame( 'baz foo qux', $x->getProcessed() );

        $x = StringParser::parseStringEx( 'baz `foo` qux' );
        $x->substBackQuotes( function () {
            return 'bar';
        } );
        self::assertSame( 'baz bar qux', $x->getProcessed() );
    }


    public function testSubstVariables() : void {
        $x = StringParser::parseStringEx( "foo \$bar baz" );
        self::assertTrue( $x->substVariables( [ 'bar' => 'bar' ] ) );
        self::assertEquals( 'foo bar baz', $x->getProcessed() );
    }


    public function testSubstVariablesForUndefinedVariable() : void {
        $x = StringParser::parseStringEx( "foo \$bar baz" );
        $y = $x->substVariables( [] );
        self::assertIsString( $y );
        self::assertStringContainsString( 'Undefined', strval( $y ) );
    }


}
