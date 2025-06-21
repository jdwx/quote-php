<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Operators\DelimiterOperator;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


#[CoversClass( DelimiterOperator::class )]
final class DelimiterOperatorTest extends OperatorTestCase {


    public function testMatch() : void {
        $delim = DelimiterOperator::whitespace();
        self::assertNull( $delim->match( 'Foo' ) );
        self::assertPiece( ' ', ' ', '', $delim->match( ' ' ) );
        self::assertPiece( ' ', ' ', 'Foo', $delim->match( ' Foo' ) );
        self::assertPiece( "   \t  ", "   \t  ", 'Foo', $delim->match( "   \t  Foo" ) );
    }


}
