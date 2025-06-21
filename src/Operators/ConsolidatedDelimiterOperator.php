<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


class ConsolidatedDelimiterOperator extends DelimiterOperator {


    public function __construct( array $rDelimiters, private readonly string $stConsolidated ) {
        parent::__construct( $rDelimiters );
    }


    public static function whitespace() : self {
        return new self( self::WHITESPACE_DELIMITERS, ' ' );
    }


    protected function replace( string $i_stMatch ) : ?string {
        return $this->stConsolidated;
    }


}
