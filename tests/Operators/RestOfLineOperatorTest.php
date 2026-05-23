<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Operators\RestOfLineOperator;
use JDWX\Quote\Piece;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


class RestOfLineOperatorTest extends OperatorTestCase {


    public function testMatch() : void {
        $st = '# This is a test.';
        $op = RestOfLineOperator::shComment();
        $piece = $op->match( $st );
        assert( $piece instanceof Piece );
        self::assertPiece( '# This is a test.', 'This is a test.', '', $piece );
    }


    public function testMatchForMultiline() : void {
        $st = "# Foo\nBar";
        $op = RestOfLineOperator::shComment();
        $piece = $op->match( $st );
        assert( $piece instanceof Piece );
        self::assertPiece( "# Foo\n", 'Foo', 'Bar', $piece );
    }


    public function testMatchForNoMatch() : void {
        $st = 'This is a test. # This is only a test.';
        $op = RestOfLineOperator::shComment();
        self::assertNull( $op->match( $st ) );
    }


}
