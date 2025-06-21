<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators\Escape;


use JDWX\Quote\Piece;


class ControlCharEscape extends AbstractEscape {


    private const ESCAPE_MAP = [
        '\r' => "\r",
        '\n' => "\n",
        '\t' => "\t",
        '\v' => "\v",
        '\f' => "\f",
        '\0' => "\0",

        # I wish I remembered why I did this in the original version.
        # The code later stripped the backslash anyway, so this was redundant.
        # Did I mean to replace with '\\a' and '\\b' for some reason?
        '\a' => '\a',
        '\b' => '\b',
    ];


    public function match( string $i_st ) : ?Piece {
        foreach ( array_keys( self::ESCAPE_MAP ) as $stMatch ) {
            $result = $this->matchOne( $i_st, $stMatch );
            if ( $result instanceof Piece ) {
                return $result;
            }
        }
        return null;
    }


    protected function replace( string $i_stMatch ) : string {
        return self::ESCAPE_MAP[ $i_stMatch ];
    }


    private function matchOne( string $i_st, string $i_stMatch ) : ?Piece {
        if ( ! str_starts_with( $i_st, $i_stMatch ) ) {
            return null;
        }
        return $this->result( $i_stMatch, $i_st );
    }


}
