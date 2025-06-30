<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Exception;
use JDWX\Quote\Piece;


class QuoteOperator extends AbstractOperator {


    private readonly string $stClose;


    public function __construct( private readonly string $stOpen,
                                 ?string                 $i_nstClose = null,
                                 private readonly bool   $bIgnoreUnclosed = false ) {
        $this->stClose = $i_nstClose ?? $stOpen;
    }


    public static function backtick( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( '`', null, $i_bIgnoreUnclosed );
    }


    public static function cComment( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( '/*', '*/', $i_bIgnoreUnclosed );
    }


    public static function double( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( '"', null, $i_bIgnoreUnclosed );
    }


    public static function simple( bool $i_bIgnoreUnclosed = false ) : MultiOperator {
        return new MultiOperator( [
            QuoteOperator::single( $i_bIgnoreUnclosed ),
            QuoteOperator::double( $i_bIgnoreUnclosed ),
        ] );
    }


    public static function single( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( "'", null, $i_bIgnoreUnclosed );
    }


    public static function varCurly( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( '${', '}', $i_bIgnoreUnclosed );
    }


    public static function varParen( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( '$(', ')', $i_bIgnoreUnclosed );
    }


    /**
     * @param string $i_st
     * @return Piece|null
     * @throws Exception
     */
    public function match( string $i_st ) : ?Piece {
        if ( ! str_starts_with( $i_st, $this->stOpen ) ) {
            return null;
        }
        $stMatch = $this->stOpen;
        $stRest = mb_substr( $i_st, mb_strlen( $stMatch ) );
        $bDone = false;

        while ( false !== ( $uPos = mb_strpos( $stRest, $this->stClose ) ) ) {
            if ( 0 !== $uPos && mb_substr( $stRest, $uPos - 1, 1 ) === '\\' ) {
                // This is an escaped close quote.
                $st = mb_substr( $stRest, 0, $uPos - 1 );
                $stMatch .= $st . '\\' . $this->stClose;
                $stRest = mb_substr( $stRest, strlen( $st ) + strlen( $this->stClose ) + 1 );
                continue;
            }
            $st = mb_substr( $stRest, 0, $uPos );
            $st .= $this->stClose;
            $stMatch .= $st;
            $bDone = true;
            break;
        }
        if ( ! $bDone ) {
            if ( ! $this->bIgnoreUnclosed ) {
                throw new Exception( "Unclosed quote in string: {$i_st}" );
            }
            // No closing quote found
            return null;
        }
        return $this->result( $stMatch, $i_st );
    }


    protected function replace( string $i_stMatch ) : ?string {
        $i_stMatch = substr( $i_stMatch, strlen( $this->stOpen ) );
        $i_stMatch = substr( $i_stMatch, 0, -strlen( $this->stClose ) );
        $i_stMatch = str_replace( '\\' . $this->stClose, $this->stClose, $i_stMatch );
        return $i_stMatch;
    }


}
