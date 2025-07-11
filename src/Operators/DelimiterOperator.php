<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Piece;


class DelimiterOperator extends AbstractOperator {


    protected const WHITESPACE_DELIMITERS = [ ' ', "\t", "\r", "\n" ];


    /** @param list<string> $rDelimiters */
    public function __construct( private readonly array $rDelimiters ) {}


    public static function whitespace() : self {
        return new self( self::WHITESPACE_DELIMITERS );
    }


    public function match( string $i_st ) : ?Piece {
        $stMatch = '';
        $stRest = $i_st;
        while ( '' !== $stRest ) {
            $stFound = '';
            foreach ( $this->rDelimiters as $stDelimiter ) {
                if ( str_starts_with( $stRest, $stDelimiter ) ) {
                    // Match the delimiter at the start of the string.
                    $stFound = $stDelimiter;
                    $stMatch .= $stFound;
                    $stRest = substr( $stRest, strlen( $stFound ) );
                    break;
                }
            }
            if ( '' === $stFound ) {
                break;
            }
        }
        if ( '' === $stMatch ) {
            return null;
        }
        return $this->result( $stMatch, $i_st );
    }


    protected function replace( string $i_stMatch ) : ?string {
        return $i_stMatch;
    }


}
