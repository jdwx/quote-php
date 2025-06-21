<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\Variables;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Variables::class )]
final class VariablesTest extends TestCase {


    public function testInvoke() : void {
        $vars = new Variables( [ 'foo' => 'Bar' ] );
        self::assertSame( 'Bar', $vars( 'foo' ) );
        self::assertSame( '', $vars( 'baz' ) );
    }


}
