<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Piece;


class RestOfLineOperator extends AbstractOperator {


    public function __construct( private readonly string $stOpen, private readonly string $stEOL = "\n" ) {}


    public static function shComment() : self {
        return new self( '#' );
    }


    /**
     * @inheritDoc
     */
    public function match( string $i_st ) : ?Piece {
        if ( ! str_starts_with( $i_st, $this->stOpen ) ) {
            return null;
        }
        $uPos = strpos( $i_st, $this->stEOL );
        $stMatch = ( false === $uPos ) ? $i_st : substr( $i_st, 0, $uPos + 1 );
        return $this->result( $stMatch, $i_st );
    }


    protected function replace( string $i_stMatch ) : ?string {
        $st = trim( ltrim( $i_stMatch, '#' ) );
        return $st;
    }


}
