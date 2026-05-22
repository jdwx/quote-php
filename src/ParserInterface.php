<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


interface ParserInterface {


    /**
     * @return iterable<string>
     * @throws Exception
     */
    public function __invoke( string $i_st ) : iterable;


    public function parse( string $i_st ) : \Generator;


}