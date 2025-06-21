<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


readonly class Segment {


    public function __construct( public SegmentType $type, public string $value, public string $original ) {}


    /**
     * @param iterable<Segment> $i_segments
     * @return iterable<Segment>
     */
    public static function coalesce( iterable $i_segments ) : iterable {
        $current = null;
        foreach ( $i_segments as $segment ) {
            if ( $segment->type->canCoalesce() ) {
                if ( null === $current ) {
                    $current = $segment;
                    continue;
                }
                if ( $segment->type === $current->type ) {
                    $current = $current->merge( $segment );
                    continue;
                }
                yield $current;
                $current = $segment;
                continue;
            }

            if ( $current instanceof Segment ) {
                yield $current;
                $current = null;
            }

            yield $segment;
        }
        if ( $current instanceof Segment ) {
            yield $current;
        }
    }


    /**
     * @param iterable<Segment> $i_segments
     * @return iterable<Segment>
     */
    public static function dropComments( iterable $i_segments ) : iterable {
        yield from self::dropType( $i_segments, SegmentType::COMMENT );
    }


    /**
     * @param iterable<Segment> $i_segments
     * @return iterable<Segment>
     */
    public static function dropDelimiters( iterable $i_segments ) : iterable {
        foreach ( $i_segments as $segment ) {
            if ( $segment->type !== SegmentType::DELIMITER ) {
                yield $segment;
            }
        }
    }


    /**
     * @param iterable<Segment> $i_segments
     * @param list<SegmentType>|SegmentType $i_type
     * @return iterable<Segment>
     */
    public static function dropType( iterable $i_segments, array|SegmentType $i_type ) : iterable {
        if ( ! is_array( $i_type ) ) {
            $i_type = [ $i_type ];
        }
        foreach ( $i_segments as $segment ) {
            if ( ! in_array( $segment->type, $i_type, true ) ) {
                yield $segment;
            }
        }
    }


    /** @param iterable<Segment> $i_segments */
    public static function mergeOriginal( iterable $i_segments ) : string {
        $st = '';
        foreach ( $i_segments as $segment ) {
            $st .= $segment->original;
        }
        return $st;
    }


    /** @param iterable<Segment> $i_segments */
    public static function mergeValues( iterable $i_segments ) : string {
        $st = '';
        foreach ( self::coalesce( self::dropComments( $i_segments ) ) as $segment ) {
            $st .= $segment->value;
        }
        return $st;
    }


    /**
     * @param iterable<Segment> $i_segments
     * @return iterable<Segment>
     */
    public static function simplify( iterable $i_segments ) : iterable {
        yield from self::dropType( self::coalesce( $i_segments ), [ SegmentType::COMMENT, SegmentType::DELIMITER ] );
    }


    /**
     * @param iterable<Segment> $i_segments
     * @return iterable<string>
     */
    public static function values( iterable $i_segments ) : iterable {
        foreach ( $i_segments as $segment ) {
            yield $segment->value;
        }
    }


    public function append( string $i_value, string $i_original ) : self {
        return new self(
            $this->type,
            $this->value . $i_value,
            $this->original . $i_original
        );
    }


    public function merge( Segment $i_from ) : self {
        if ( $i_from->type !== $this->type ) {
            throw new \InvalidArgumentException( 'Cannot merge segments of different types.' );
        }
        return new self(
            $this->type,
            $this->value . $i_from->value,
            $this->original . $i_from->original
        );
    }


}
