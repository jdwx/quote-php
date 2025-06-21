<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Tests;


use JDWX\Quote\SegmentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SegmentType::class )]
final class SegmentTypeTest extends TestCase {


    public function testAllowComments() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowComments() );
        self::assertFalse( SegmentType::COMMENT->allowComments() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowComments() );
        self::assertFalse( SegmentType::SOFT_QUOTED->allowComments() );
        self::assertFalse( SegmentType::STRONG_CALLBACK->allowComments() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowComments() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowComments() );
        self::assertFalse( SegmentType::ESCAPE->allowComments() );
        self::assertFalse( SegmentType::DELIMITER->allowComments() );
        self::assertFalse( SegmentType::LITERAL->allowComments() );
    }


    public function testAllowDelimiters() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowDelimiters() );
        self::assertFalse( SegmentType::COMMENT->allowDelimiters() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowDelimiters() );
        self::assertTrue( SegmentType::SOFT_QUOTED->allowDelimiters() );
        self::assertTrue( SegmentType::STRONG_CALLBACK->allowDelimiters() );
        self::assertTrue( SegmentType::WEAK_CALLBACK->allowDelimiters() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowDelimiters() );
        self::assertFalse( SegmentType::ESCAPE->allowDelimiters() );
        self::assertFalse( SegmentType::DELIMITER->allowDelimiters() );
        self::assertFalse( SegmentType::LITERAL->allowDelimiters() );
    }


    public function testAllowEscaping() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowEscapes() );
        self::assertFalse( SegmentType::COMMENT->allowEscapes() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowEscapes() );
        self::assertTrue( SegmentType::SOFT_QUOTED->allowEscapes() );
        self::assertTrue( SegmentType::STRONG_CALLBACK->allowEscapes() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowEscapes() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowEscapes() );
        self::assertFalse( SegmentType::ESCAPE->allowEscapes() );
        self::assertFalse( SegmentType::DELIMITER->allowEscapes() );
        self::assertFalse( SegmentType::LITERAL->allowEscapes() );
    }


    public function testAllowHardQuotes() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowHardQuotes() );
        self::assertFalse( SegmentType::COMMENT->allowHardQuotes() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowHardQuotes() );
        self::assertFalse( SegmentType::SOFT_QUOTED->allowHardQuotes() );
        self::assertTrue( SegmentType::STRONG_CALLBACK->allowHardQuotes() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowHardQuotes() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowHardQuotes() );
        self::assertFalse( SegmentType::ESCAPE->allowHardQuotes() );
        self::assertFalse( SegmentType::DELIMITER->allowHardQuotes() );
        self::assertFalse( SegmentType::LITERAL->allowHardQuotes() );
    }


    public function testAllowOpenCallbacks() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowOpenCallbacks() );
        self::assertFalse( SegmentType::COMMENT->allowOpenCallbacks() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowOpenCallbacks() );
        self::assertTrue( SegmentType::SOFT_QUOTED->allowOpenCallbacks() );
        self::assertTrue( SegmentType::STRONG_CALLBACK->allowOpenCallbacks() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowOpenCallbacks() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowOpenCallbacks() );
        self::assertFalse( SegmentType::ESCAPE->allowOpenCallbacks() );
        self::assertFalse( SegmentType::DELIMITER->allowOpenCallbacks() );
        self::assertFalse( SegmentType::LITERAL->allowOpenCallbacks() );
    }


    public function testAllowSoftQuotes() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowSoftQuotes() );
        self::assertFalse( SegmentType::COMMENT->allowSoftQuotes() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowSoftQuotes() );
        self::assertFalse( SegmentType::SOFT_QUOTED->allowSoftQuotes() );
        self::assertFalse( SegmentType::STRONG_CALLBACK->allowSoftQuotes() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowSoftQuotes() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowSoftQuotes() );
        self::assertFalse( SegmentType::ESCAPE->allowSoftQuotes() );
        self::assertFalse( SegmentType::DELIMITER->allowSoftQuotes() );
        self::assertFalse( SegmentType::LITERAL->allowSoftQuotes() );
    }


    public function testAllowStrongCallbacks() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowStrongCallbacks() );
        self::assertFalse( SegmentType::COMMENT->allowStrongCallbacks() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowStrongCallbacks() );
        self::assertTrue( SegmentType::SOFT_QUOTED->allowStrongCallbacks() );
        self::assertFalse( SegmentType::STRONG_CALLBACK->allowStrongCallbacks() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowStrongCallbacks() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowStrongCallbacks() );
        self::assertFalse( SegmentType::ESCAPE->allowStrongCallbacks() );
        self::assertFalse( SegmentType::DELIMITER->allowStrongCallbacks() );
        self::assertFalse( SegmentType::LITERAL->allowStrongCallbacks() );
    }


    public function testAllowWeakCallbacks() : void {
        self::assertTrue( SegmentType::UNDEFINED->allowWeakCallbacks() );
        self::assertFalse( SegmentType::COMMENT->allowWeakCallbacks() );
        self::assertFalse( SegmentType::HARD_QUOTED->allowWeakCallbacks() );
        self::assertTrue( SegmentType::SOFT_QUOTED->allowWeakCallbacks() );
        self::assertTrue( SegmentType::STRONG_CALLBACK->allowWeakCallbacks() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->allowWeakCallbacks() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->allowWeakCallbacks() );
        self::assertFalse( SegmentType::ESCAPE->allowWeakCallbacks() );
        self::assertFalse( SegmentType::DELIMITER->allowWeakCallbacks() );
        self::assertFalse( SegmentType::LITERAL->allowWeakCallbacks() );
    }


    public function testCanCoalesce() : void {
        self::assertTrue( SegmentType::DELIMITER->canCoalesce() );
        self::assertTrue( SegmentType::LITERAL->canCoalesce() );
        self::assertFalse( SegmentType::COMMENT->canCoalesce() );
        self::assertFalse( SegmentType::HARD_QUOTED->canCoalesce() );
        self::assertFalse( SegmentType::SOFT_QUOTED->canCoalesce() );
        self::assertFalse( SegmentType::STRONG_CALLBACK->canCoalesce() );
        self::assertFalse( SegmentType::WEAK_CALLBACK->canCoalesce() );
        self::assertFalse( SegmentType::OPEN_CALLBACK->canCoalesce() );
        self::assertFalse( SegmentType::ESCAPE->canCoalesce() );
    }


}
