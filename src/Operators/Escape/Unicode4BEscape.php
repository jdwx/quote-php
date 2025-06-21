<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators\Escape;


use JDWX\Quote\Piece;
use JDWX\Strict\OK;


class Unicode4BEscape extends AbstractEscape {


    public function match( string $i_st ) : ?Piece {
        if ( preg_match( '/^(\\\\U\+[0-9a-fA-F]{1,8})/', $i_st, $matches ) ) {
            return $this->result( $matches[ 0 ], $i_st );
        }
        if ( preg_match( '/^(\\\\U{[0-9a-fA-F]{1,8})}/', $i_st, $matches ) ) {
            return $this->result( $matches[ 0 ], $i_st );
        }
        return null;
    }


    protected function replace( string $i_stMatch ) : string {
        $i_stMatch = substr( $i_stMatch, 2 ); // Remove the leading \U or \u
        if ( str_starts_with( $i_stMatch, '+' ) ) {
            $i_stMatch = substr( $i_stMatch, 1 ); // Remove the leading +
        } elseif ( str_starts_with( $i_stMatch, '{' ) && str_ends_with( $i_stMatch, '}' ) ) {
            $i_stMatch = substr( $i_stMatch, 1, -1 ); // Remove the surrounding {}
        }
        $i_stMatch = str_pad( $i_stMatch, 8, '0', STR_PAD_LEFT ); // Pad to 8 characters
        return OK::mb_convert_encoding( pack( 'H*', $i_stMatch ), 'UTF-8', 'UCS-4BE' );
    }


}
