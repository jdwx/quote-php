<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Escape;


use JDWX\Quote\Piece;


class OctalEscape extends AbstractEscape {


    public function match( string $i_st ) : ?Piece {
        if ( ! preg_match( '/^(\\\\[0-7]{3})/', $i_st, $matches ) ) {
            return null;
        }
        return $this->result( $matches[ 0 ], $i_st );
    }


    protected function replace( string $i_stMatch ) : string {
        $i_stMatch = substr( $i_stMatch, 1 ); // Remove the leading backslash
        return chr( octdec( $i_stMatch ) );
    }


}
