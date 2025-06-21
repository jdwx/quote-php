<?php /** @noinspection PhpClassNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Quote\Tests\Operators;


use JDWX\Quote\Operators\ConsolidatedDelimiterOperator;
use JDWX\Quote\Tests\Helpers\OperatorTestCase;


require_once __DIR__ . '/../Helpers/OperatorTestCase.php';


class ConsolidatedDelimiterOperatorTest extends OperatorTestCase {


    public function testReplace() : void {
        $delim = ConsolidatedDelimiterOperator::whitespace();
        self::assertPiece( '   ', ' ', '', $delim->match( '   ' ) );
    }


}
