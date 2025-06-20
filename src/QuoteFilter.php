<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


class QuoteFilter extends AbstractOperator {


    private readonly string $stClose;


    public function __construct( private readonly Segment $segment,
                                 private readonly string  $stOpen,
                                 ?string                  $i_nstClose = null,
                                 private readonly bool    $bIgnoreUnclosed = false ) {
        $this->stClose = $i_nstClose ?? $stOpen;
    }


    public static function backtick( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( Segment::CALLBACK_QUOTED, '`', null, $i_bIgnoreUnclosed );
    }


    public static function double( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( Segment::SOFT_QUOTED, '"', null, $i_bIgnoreUnclosed );
    }


    public static function single( bool $i_bIgnoreUnclosed = false ) : self {
        return new self( Segment::HARD_QUOTED, "'", null, $i_bIgnoreUnclosed );
    }


    public function match( string $i_st ) : ?Piece {
        if ( ! str_starts_with( $i_st, $this->stOpen ) ) {
            return null;
        }
        $stMatch = $this->stOpen;
        $stOut = '';
        $stRest = mb_substr( $i_st, mb_strlen( $stMatch ) );
        $bDone = false;

        while ( false !== ( $uPos = mb_strpos( $stRest, $this->stClose ) ) ) {
            if ( 0 === $uPos || mb_substr( $stRest, $uPos - 1, 1 ) === '\\' ) {
                // This is an escaped close quote, so we de-escape and skip it
                $st = mb_substr( $stRest, 0, $uPos - 1 );
                $stMatch .= $st . '\\' . $this->stClose;
                $stOut .= $st . $this->stClose;
                $stRest = mb_substr( $stRest, strlen( $st ) + strlen( $this->stClose ) + 1 );
                continue;
            }
            $st = mb_substr( $stRest, 0, $uPos );
            $stOut .= $st;
            $st .= $this->stClose;
            $stMatch .= $st;
            $stRest = mb_substr( $stRest, strlen( $st ) );
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
        return new Piece( $stMatch, $stOut, $stRest, $this->segment );
    }


    protected function replace( string $i_stMatch ) : ?string {
        $i_stMatch = substr( $i_stMatch, strlen( $this->stOpen ) );
        $i_stMatch = substr( $i_stMatch, 0, -strlen( $this->stClose ) );
        return $i_stMatch;
    }


}
