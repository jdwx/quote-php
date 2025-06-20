<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


interface OperatorInterface {


    public function __invoke( string $i_st ) : string;


    public function match( string $i_st ) : ?Piece;


}