<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Piece;


abstract class AbstractOperator implements OperatorInterface {


    public function __invoke( string $i_st ) : string {
        $stOut = '';
        while ( $i_st !== '' ) {
            $match = $this->match( $i_st );
            if ( $match === null ) {
                $stOut .= substr( $i_st, 0, 1 );
                $i_st = substr( $i_st, 1 );
                continue;
            }
            $stOut .= $match->stReplace;
            $i_st = $match->stRest;
        }
        return $stOut;
    }


    protected function makePiece( string $stMatch, string $stReplace, string $stRest ) : Piece {
        return new Piece( $stMatch, $stReplace, $stRest );
    }


    abstract protected function replace( string $i_stMatch ) : ?string;


    protected function result( string $stMatch, string $stText ) : ?Piece {
        $nstReplace = $this->replace( $stMatch );
        if ( ! is_string( $nstReplace ) ) {
            return null;
        }
        $stRest = substr( $stText, strlen( $stMatch ) );
        return $this->makePiece( $stMatch, $nstReplace, $stRest );
    }


}
