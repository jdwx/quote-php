<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Operators\Escape\AbstractEscape;
use JDWX\Quote\Piece;


class MultiOperator extends AbstractEscape {


    /** @var list<OperatorInterface> */
    private array $escapes;


    /** @param list<OperatorInterface>|OperatorInterface $i_escapes */
    public function __construct( array|OperatorInterface $i_escapes ) {
        if ( ! is_array( $i_escapes ) ) {
            $i_escapes = [ $i_escapes ];
        }
        $this->escapes = $i_escapes;
    }


    public function match( string $i_st ) : ?Piece {
        foreach ( $this->escapes as $escape ) {
            $match = $escape->match( $i_st );
            if ( $match !== null ) {
                return $match;
            }
        }
        return null;
    }


    /** @codeCoverageIgnore Not used. */
    protected function replace( string $i_stMatch ) : ?string {
        return null;
    }


}
