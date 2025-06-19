<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\StringParser;


class MyStringParser extends StringParser {


    /**
     * @return list<string>|string
     * Leaks a protected function for testing purposes.
     */
    public static function myParseQuote( string $i_st, string $i_stQuoteCharacter ) : array|string {
        return parent::parseQuote( $i_st, $i_stQuoteCharacter );
    }


}
