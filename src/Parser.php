<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


use JDWX\Quote\Operators\OperatorInterface;


readonly class Parser {


    /** @var (callable( string ) : string)|null */
    private mixed $fnStrong;

    /** @var (callable( string ) : string)|null */
    private mixed $fnWeak;

    /** @var (callable( string ) : string)|null */
    private mixed $fnOpen;


    public function __construct(
        private ?OperatorInterface $comment = null,
        private ?OperatorInterface $hardQuote = null,
        private ?OperatorInterface $softQuote = null,
        private ?OperatorInterface $strongCallback = null,
        private ?OperatorInterface $weakCallback = null,
        private ?OperatorInterface $openCallback = null,
        private ?OperatorInterface $escape = null,
        private ?OperatorInterface $delimiter = null,
        ?callable                  $fnStrong = null,
        ?callable                  $fnWeak = null,
        ?callable                  $fnOpen = null
    ) {
        $this->fnStrong = $fnStrong;
        $this->fnWeak = $fnWeak;
        $this->fnOpen = $fnOpen;
    }


    /**
     * @return iterable<string>
     * @throws Exception
     */
    public function __invoke( string $i_st ) : iterable {
        return Segment::values( Segment::simplify( $this->parse( $i_st ) ) );
    }


    /**
     * @return \Generator<int, Segment>
     * @throws Exception
     */
    public function parse( string $i_st ) : \Generator {
        # Iterate over this to make sure the keys are consecutive integers.
        foreach ( $this->parseSegment( SegmentType::UNDEFINED, $i_st ) as $segment ) {
            yield $segment;
        }
    }


    protected function comment( string $i_stOriginal ) : \Generator {
        yield new Segment( SegmentType::COMMENT, '', $i_stOriginal );
    }


    protected function delimiter( string $i_stValue, string $i_stOriginal ) : \Generator {
        yield new Segment( SegmentType::DELIMITER, $i_stValue, $i_stOriginal );
    }


    protected function escape( string $i_stValue, string $i_stOriginal ) : \Generator {
        $st = Segment::mergeValues( $this->parseSegment( SegmentType::ESCAPE, $i_stValue ) );
        yield new Segment( SegmentType::LITERAL, $st, $i_stOriginal );
    }


    protected function hardQuote( string $i_stValue, string $i_stOriginal ) : \Generator {
        yield new Segment( SegmentType::HARD_QUOTED, $i_stValue, $i_stOriginal );
    }


    protected function openCallback( string $i_stValue, string $i_stOriginal ) : \Generator {
        $st = Segment::mergeValues( $this->parseSegment( SegmentType::OPEN_CALLBACK, $i_stValue ) );
        $st = $this->fnOpen ? ( $this->fnOpen )( $st ) : $st;
        yield new Segment( SegmentType::OPEN_CALLBACK, $st, $i_stOriginal );
    }


    /**
     * @param SegmentType $i_type
     * @param string $i_st
     * @return \Generator<Segment>
     * @throws Exception
     */
    protected function parseSegment( SegmentType $i_type, string $i_st ) : \Generator {
        $stRest = $i_st;
        while ( '' !== $stRest ) {

            if ( $i_type->allowComments() ) {
                $match = $this->comment?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->comment( $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowHardQuotes() ) {
                $match = $this->hardQuote?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->hardQuote( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowSoftQuotes() ) {
                $match = $this->softQuote?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->softQuote( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowStrongCallbacks() ) {
                $match = $this->strongCallback?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->strongCallback( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowWeakCallbacks() ) {
                $match = $this->weakCallback?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->weakCallback( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowOpenCallbacks() ) {
                $match = $this->openCallback?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->openCallback( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowEscapes() ) {
                $match = $this->escape?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->escape( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            if ( $i_type->allowDelimiters() ) {
                $match = $this->delimiter?->match( $stRest );
                if ( $match instanceof Piece ) {
                    $stRest = $match->stRest;
                    yield from $this->delimiter( $match->stReplace, $match->stMatch );
                    continue;
                }
            }

            $stFirst = substr( $stRest, 0, 1 );
            $stRest = substr( $stRest, 1 );
            yield new Segment( SegmentType::LITERAL, $stFirst, $stFirst );

        }
    }


    protected function softQuote( string $i_stValue, string $i_stOriginal ) : \Generator {
        $st = Segment::mergeValues( $this->parseSegment( SegmentType::SOFT_QUOTED, $i_stValue ) );
        yield new Segment( SegmentType::SOFT_QUOTED, $st, $i_stOriginal );
    }


    protected function strongCallback( string $i_stValue, string $i_stOriginal ) : \Generator {
        $st = Segment::mergeValues( $this->parseSegment( SegmentType::STRONG_CALLBACK, $i_stValue ) );
        $st = $this->fnStrong ? ( $this->fnStrong )( $st ) : $st;
        yield new Segment( SegmentType::STRONG_CALLBACK, $st, $i_stOriginal );
    }


    protected function weakCallback( string $i_stValue, string $i_stOriginal ) : \Generator {
        $st = Segment::mergeValues( $this->parseSegment( SegmentType::WEAK_CALLBACK, $i_stValue ) );
        $st = $this->fnWeak ? ( $this->fnWeak )( $st ) : $st;
        yield new Segment( SegmentType::WEAK_CALLBACK, $st, $i_stOriginal );
    }


}
