<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


readonly class Variables {


    public function __construct( private array $rVariables ) {}


    public function __invoke( string $i_stName ) : string {
        return $this->rVariables[ $i_stName ] ?? '';
    }


}
