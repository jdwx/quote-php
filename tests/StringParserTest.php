<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\StringParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


require_once __DIR__ . '/MyStringParser.php';


#[CoversClass( StringParser::class )]
class StringParserTest extends TestCase {


    public function testParseQuoteForDoubleQuote() : void {
        $x = MyStringParser::myParseQuote( 'foo" bar', '"' );
        assert( is_array( $x ) );
        self::assertCount( 2, $x );
        self::assertSame( 'foo', $x[ 0 ] );
        self::assertSame( ' bar', $x[ 1 ] );
    }


    public function testParseQuoteForDoubleQuoteWithEscapedQuote() : void {
        $x = MyStringParser::myParseQuote( 'foo\" bar" baz', '"' );
        assert( is_array( $x ) );
        self::assertCount( 2, $x );
        self::assertSame( 'foo" bar', $x[ 0 ] );
        self::assertSame( ' baz', $x[ 1 ] );
    }


    public function testParseQuoteForEscapedBackslash() : void {
        $x = MyStringParser::myParseQuote( 'foo\\ bar" baz', '"' );
        assert( is_array( $x ) );
        self::assertCount( 2, $x );
        self::assertSame( 'foo\\ bar', $x[ 0 ] );
        self::assertSame( ' baz', $x[ 1 ] );
    }


    public function testParseQuoteForSingleQuote() : void {
        $x = MyStringParser::myParseQuote( "foo' bar", "'" );
        assert( is_array( $x ) );
        self::assertCount( 2, $x );
        self::assertSame( 'foo', $x[ 0 ] );
        self::assertSame( ' bar', $x[ 1 ] );
    }


    public function testParseQuoteForSingleQuoteWithEscapedQuote() : void {
        $x = MyStringParser::myParseQuote( "foo\\' bar' baz", "'" );
        assert( is_array( $x ) );
        self::assertCount( 2, $x );
        self::assertSame( "foo' bar", $x[ 0 ] );
        self::assertSame( ' baz', $x[ 1 ] );
    }


    public function testParseQuoteForUnterminatedQuote() : void {
        $x = MyStringParser::myParseQuote( 'foo bar', "'" );
        self::assertIsString( $x );
        self::assertStringContainsString( 'Unmatched', strval( $x ) );
    }


    public function testParseStringExForFailure() : void {
        self::expectException( RuntimeException::class );
        StringParser::parseStringEx( 'foo bar baz\\' );
    }


    public function testParseStringForBackQuotes() : void {
        $x = StringParser::parseStringEx( '`foo` bar' );
        self::assertCount( 3, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );

        $x = StringParser::parseStringEx( '`foo` bar', i_bBackquotes: false );
        self::assertCount( 3, $x );
        self::assertSame( '`foo`', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );
    }


    public function testParseStringForBackQuotesWithEscapedBackQuote() : void {
        $x = StringParser::parseStringEx( '`foo\\`bar`' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo`bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForBackQuotesWithMissingEndQuote() : void {
        $x = StringParser::parseString( '`foo' );
        self::assertIsString( $x );
        self::assertStringContainsString( 'Unmatched', strval( $x ) );
    }


    public function testParseStringForBackslashAsLastCharacter() : void {
        $x = StringParser::parseString( 'foo\\' );
        self::assertIsString( $x );
        self::assertStringContainsString( 'Hanging', strval( $x ) );
    }


    public function testParseStringForBackslashNewline() : void {
        /** @noinspection SpellCheckingInspection */
        $x = StringParser::parseStringEx( "foo\\nbar" );
        self::assertCount( 3, $x );
        self::assertSame( '\\n', $x->getSegment( 1 )->getOriginal() );
        self::assertSame( "\n", $x->getSegment( 1 )->getProcessed() );
    }


    public function testParseStringForBackslashOctal() : void {
        $x = StringParser::parseStringEx( 'foo\\101bar' );
        self::assertCount( 3, $x );
        self::assertSame( '\\101', $x->getSegment( 1 )->getOriginal() );
        self::assertSame( 'A', $x->getSegment( 1 )->getProcessed() );
    }


    public function testParseStringForBackslashUnicode() : void {
        $x = StringParser::parseStringEx( 'foo\\u00C3bar' );
        self::assertCount( 3, $x );
        self::assertSame( '\\u00C3', $x->getSegment( 1 )->getOriginal() );
        self::assertSame( 'Ã', $x->getSegment( 1 )->getProcessed() );
    }


    public function testParseStringForCommentInQuotes() : void {
        $x = StringParser::parseStringEx( '"foo # bar"' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo # bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForCommentPartialLine() : void {
        $x = StringParser::parseStringEx( 'foo # bar' );
        self::assertCount( 3, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( '', $x->getSegment( 2 )->getProcessed() );
    }


    public function testParseStringForCommentWholeLine() : void {
        $x = StringParser::parseStringEx( '# foo' );
        self::assertCount( 1, $x );
        self::assertSame( '', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForDoubleQuoteMissingEndQuote() : void {
        $x = StringParser::parseString( 'foo "bar' );
        self::assertIsString( $x );
        self::assertStringContainsString( 'Unmatched', strval( $x ) );
    }


    public function testParseStringForDoubleQuotedWord() : void {
        $x = StringParser::parseStringEx( '"foo"' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForDoubleQuotedWordEscapedQuote() : void {
        $x = StringParser::parseStringEx( '"foo\""' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo"', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForDoubleQuotedWords() : void {
        $x = StringParser::parseStringEx( '"foo bar"' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForDoubleQuotes() : void {
        $x = StringParser::parseStringEx( '"foo" bar' );
        self::assertCount( 3, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );

        $x = StringParser::parseStringEx( '"foo" bar', i_bDoubleQuotes: false );
        self::assertCount( 3, $x );
        self::assertSame( '"foo"', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );
    }


    public function testParseStringForEmpty() : void {
        $x = StringParser::parseStringEx( '' );
        self::assertCount( 0, $x );
    }


    public function testParseStringForSingleQuoteMissingEndQuote() : void {
        $x = StringParser::parseString( "foo 'bar" );
        self::assertIsString( $x );
        self::assertStringContainsString( 'Unmatched', strval( $x ) );
    }


    public function testParseStringForSingleQuoteWithEscapeSequence() : void {
        $x = StringParser::parseStringEx( "'foo\\n bar'" );
        self::assertCount( 1, $x );
        self::assertSame( 'foo\\n bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForSingleQuotedWord() : void {
        $x = StringParser::parseStringEx( "'foo'" );
        self::assertCount( 1, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForSingleQuotedWordEscapedQuote() : void {
        $x = StringParser::parseStringEx( "'foo\\' bar'" );
        self::assertCount( 1, $x );
        self::assertSame( "foo' bar", $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForSingleQuotedWords() : void {
        $x = StringParser::parseStringEx( "'foo bar'" );
        self::assertCount( 1, $x );
        self::assertSame( 'foo bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForSingleQuotes() : void {
        $x = StringParser::parseStringEx( "'foo' bar" );
        self::assertCount( 3, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );

        $x = StringParser::parseStringEx( "'foo' bar", i_bSingleQuotes: false );
        self::assertCount( 3, $x );
        self::assertSame( "'foo'", $x->getSegment( 0 )->getProcessed() );
        self::assertSame( ' ', $x->getSegment( 1 )->getProcessed() );
        self::assertSame( 'bar', $x->getSegment( 2 )->getProcessed() );
    }


    public function testParseStringForSingleQuotesEscapedBackslash() : void {
        $x = StringParser::parseStringEx( '\'foo\\ bar\'' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo\\ bar', $x->getSegment( 0 )->getProcessed() );
    }


    public function testParseStringForSingleWord() : void {
        $x = StringParser::parseStringEx( 'foo' );
        self::assertCount( 1, $x );
        self::assertSame( 'foo', $x->getSegment( 0 )->getProcessed() );
    }


}
