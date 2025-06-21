<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Piece;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


class AbstractOperatorTest extends OperatorTestCase {


    public function testReplaceForNull() : void {
        $op = new class() extends \JDWX\Quote\Operators\AbstractOperator {


            public function match( string $i_st ) : ?Piece {
                return $this->result( $i_st, $i_st );
            }


            protected function replace( string $i_stMatch ) : ?string {
                return null;
            }


        };
        self::assertNull( $op->match( 'Foo' ) );
    }


}
