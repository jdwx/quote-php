<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Escape;


use JDWX\Quote\Piece;
use JDWX\Strict\OK;


class Unicode2BEscape extends AbstractEscape {


    public function match( string $i_st ) : ?Piece {
        if ( ! preg_match( '/^(\\\\[Uu][0-9a-fA-F]{4})/', $i_st, $matches ) ) {
            return null;
        }
        return $this->result( $matches[ 0 ], $i_st );
    }


    protected function replace( string $i_stMatch ) : string {
        $i_stMatch = substr( $i_stMatch, 2 ); // Remove the leading \U or \u
        return OK::mb_convert_encoding( pack( 'H*', $i_stMatch ), 'UTF-8', 'UCS-2BE' );
    }


}
