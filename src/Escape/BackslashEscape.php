<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Escape;


use JDWX\Quote\Piece;


class BackslashEscape extends AbstractEscape {


    public function match( string $i_st ) : ?Piece {
        if ( preg_match( '/^\\\\./', $i_st ) ) {
            // Match a backslash followed by any character.
            $stMatch = substr( $i_st, 0, 2 );
            return $this->result( $stMatch, $i_st );
        }
        return null;
    }


    protected function replace( string $i_stMatch ) : string {
        return substr( $i_stMatch, 1, 1 );
    }


}
