<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Escape;


use JDWX\Quote\Piece;


class HexEscape extends AbstractEscape {


    public function match( string $i_st ) : ?Piece {
        if ( ! preg_match( '/^(\\\\x[0-9A-Fa-f]{2})/', $i_st, $matches ) ) {
            return null;
        }
        return $this->result( $matches[ 0 ], $i_st );
    }


    protected function replace( string $i_stMatch ) : string {
        $i_stMatch = substr( $i_stMatch, 2 ); // Remove the '\x' prefix
        return chr( hexdec( $i_stMatch ) );
    }


}
