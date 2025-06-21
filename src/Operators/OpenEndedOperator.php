<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Piece;
use JDWX\Strict\OK;
use JDWX\Strict\TypeIs;


class OpenEndedOperator extends AbstractOperator {


    private const DEFAULT_NAME_REGEX = '/^([a-zA-Z_][a-zA-Z0-9_]*)/';

    private readonly string $reName;


    public function __construct( private readonly string $stStart, ?string $i_nreName = null ) {
        $this->reName = $i_nreName ?? self::DEFAULT_NAME_REGEX;
    }


    public static function var( ?string $i_nreName = null ) : self {
        return new self( '$', $i_nreName );
    }


    public function match( string $i_st ) : ?Piece {
        if ( ! str_starts_with( $i_st, $this->stStart ) ) {
            return null;
        }
        $stMatch = $this->stStart;
        $stRest = mb_substr( $i_st, mb_strlen( $stMatch ) );
        $stMaxName = $this->findMaxMatch( $stRest );
        if ( null === $stMaxName ) {
            return null;
        }
        $stName = $this->matchName( $stMaxName );
        if ( null === $stName ) {
            return null;
        }
        $stMatch .= $stName;
        return $this->result( $stMatch, $i_st );
    }


    protected function findMaxMatch( string $i_st ) : ?string {
        if ( ! OK::preg_match( $this->reName, $i_st, $matches ) ) {
            return null;
        }
        /** @phpstan-ignore-next-line */
        assert( is_array( $matches ) );
        return TypeIs::string( $matches[ 0 ] );
    }


    protected function matchName( string $i_st ) : ?string {
        return $i_st;
    }


    protected function replace( string $i_stMatch ) : ?string {
        return mb_substr( $i_stMatch, mb_strlen( $this->stStart ) );
    }


}
