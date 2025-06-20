<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


class Delimiter extends AbstractOperator {


    protected Segment $segmentDefault = Segment::DELIMITER;


    public function __construct( private readonly array $rDelimiters = [ ' ', "\t", "\r", "\n" ] ) {}


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
