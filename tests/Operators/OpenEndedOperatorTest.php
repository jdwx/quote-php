<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Operators\OpenEndedOperator;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


class OpenEndedOperatorTest extends OperatorTestCase {


    public function testMatch() : void {
        $op = new OpenEndedOperator();
        self::assertNull( $op->match( 'Foo' ) );
        self::assertPiece( '$foo', 'foo', ' bar', $op->match( '$foo bar' ) );
        self::assertNull( $op->match( '$ nope' ) );
        self::assertNull( $op->match( '${nope}' ) );
    }


    public function testMatchWithName() : void {
        $op = new class() extends OpenEndedOperator {


            protected function matchName( string $i_st ) : ?string {
                if ( str_starts_with( $i_st, 'foo' ) ) {
                    return 'foo';
                }
                return null;
            }


        };

        self::assertPiece( '$foo', 'foo', ' bar', $op->match( '$foo bar' ) );
        self::assertNull( $op->match( '$bar' ) );
    }


}
