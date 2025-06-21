<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Helpers;


use JDWX\Quote\Piece;
use PHPUnit\Framework\TestCase;


abstract class OperatorTestCase extends TestCase {


    protected static function assertPiece( string $i_stMatch, string $i_stReplace,
                                           string $i_stRest, Piece $i_piece ) : void {
        self::assertSame( $i_stMatch, $i_piece->stMatch, 'match mismatch' );
        self::assertSame( $i_stReplace, $i_piece->stReplace, 'replace mismatch' );
        self::assertSame( $i_stRest, $i_piece->stRest, 'rest mismatch' );
    }


}
