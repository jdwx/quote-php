<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


readonly class Piece {


    public function __construct( public string  $stMatch,
                                 public string  $stReplace,
                                 public string  $stRest,
                                 public Segment $segment = Segment::UNDEFINED ) {}


}
