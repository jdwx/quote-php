<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


readonly class Parser {


    private array $operators;


    public function __construct( array|OperatorInterface $operators ) {
        if ( $operators instanceof OperatorInterface ) {
            $operators = [ $operators ];
        }
        $this->operators = $operators;
    }


    public function __invoke( string $i_st ) : array {
        $stRest = $i_st;
        $stUnquoted = '';
        $r = [];
        while ( '' !== $stRest ) {
            $match = $this->match( $stRest );
            if ( $match instanceof Piece ) {
                if ( '' !== $stUnquoted ) {
                    $r[] = new Piece( $stUnquoted, $stUnquoted, $stRest, Segment::UNQUOTED );
                    $stUnquoted = '';
                }
                $r[] = $match;
                $stRest = $match->stRest;
                continue;
            }
            $stUnquoted .= substr( $stRest, 0, 1 );
            $stRest = substr( $stRest, 1 );
        }
        if ( '' !== $stUnquoted ) {
            $r[] = new Piece( $stUnquoted, $stUnquoted, '', Segment::UNQUOTED );
        }
        return $r;
    }


    protected function match( string $i_st ) : ?Piece {
        foreach ( $this->operators as $operator ) {
            $match = $operator->match( $i_st );
            if ( $match !== null ) {
                return $match;
            }
        }
        return null;
    }


}
