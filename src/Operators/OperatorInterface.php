<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Exception;
use JDWX\Quote\Piece;


interface OperatorInterface {


    public function __invoke( string $i_st ) : string;


    /**
     * @param string $i_st
     * @return Piece|null
     * @throws Exception
     */
    public function match( string $i_st ) : ?Piece;


}