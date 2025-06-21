<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


use JDWX\Quote\Operators\MultiOperator;


class ParsedSegment {


    protected SegmentType $type;

    protected string $textProcessed;

    protected string $textOriginal;


    private MultiOperator $escapes;


    public function __construct( SegmentType $i_type, string $i_text ) {
        $this->type = $i_type;
        $this->textProcessed = $i_text;
        $this->textOriginal = $i_text;
        $this->escapes = new MultiOperator( [
            new Operators\Escape\ControlCharEscape(),
            new Operators\Escape\OctalEscape(),
            new Operators\Escape\Unicode2BEscape(),
            new Operators\Escape\BackslashEscape(),
        ] );
    }


    /** @return array<string, string|SegmentType> */
    public function debug() : array {
        return [
            'type' => $this->type,
            'textOriginal' => $this->getOriginal(),
            'textProcessed' => $this->getProcessed(),
        ];
    }


    public function getOriginal( bool $i_bIncludeComments = false ) : string {
        return match ( $this->type ) {
            SegmentType::DELIMITER, SegmentType::UNQUOTED => $this->textOriginal,
            SegmentType::HARD_QUOTED => "'" . $this->textOriginal . "'",
            SegmentType::SOFT_QUOTED => '"' . $this->textOriginal . '"',
            SegmentType::STRONG_CALLBACK => '`' . $this->textOriginal . '`',
            SegmentType::COMMENT => $i_bIncludeComments ? '#' . $this->textOriginal : '',
        };
    }


    public function getProcessed( bool $i_bIncludeQuotes = false ) : string {
        $txt = match ( $this->type ) {
            SegmentType::DELIMITER, SegmentType::HARD_QUOTED, SegmentType::STRONG_CALLBACK => $this->textProcessed,
            SegmentType::UNQUOTED, SegmentType::SOFT_QUOTED => self::substEscapeSequences( $this->textProcessed ),
            SegmentType::COMMENT => '',
        };
        if ( $i_bIncludeQuotes ) {
            if ( SegmentType::HARD_QUOTED === $this->type ) {
                $txt = "'" . $txt . "'";
            } elseif ( SegmentType::SOFT_QUOTED === $this->type ) {
                $txt = '"' . $txt . '"';
            } elseif ( SegmentType::STRONG_CALLBACK === $this->type ) {
                $txt = '`' . $txt . '`';
            }
        }
        return $txt;
    }


    public function isComment() : bool {
        return SegmentType::COMMENT === $this->type;
    }


    public function isDelimiter() : bool {
        return SegmentType::DELIMITER === $this->type;
    }


    public function substBackQuotes( callable $i_fnCallback ) : void {
        if ( SegmentType::STRONG_CALLBACK !== $this->type ) {
            return;
        }
        $this->textProcessed = $i_fnCallback( $this->textProcessed );
    }


    public function substEscapeSequences( string $st ) : string {
        return ( $this->escapes )( $st );
    }


    /** @param array<string, string> $i_rVariables */
    public function substVariables( array $i_rVariables ) : true|string {
        if ( SegmentType::HARD_QUOTED === $this->type ) {
            return true;
        }
        $bst = $this->substVariablesWithBraces( $i_rVariables );
        if ( true !== $bst ) {
            return $bst;
        }
        return $this->substVariablesBare( $i_rVariables );
    }


    /**
     * @param array<string, string> $i_rVariables
     * phpStan really struggles with the callback changing the type of $bst in this method.
     * @phpstan-ignore return.unusedType
     */
    private function substVariablesBare( array $i_rVariables ) : true|string {
        $bst = true;
        $st = preg_replace_callback( '/\$([a-zA-Z_][a-zA-Z0-9_]*)/', function ( $matches ) use ( &$i_rVariables, &$bst ) {
            /** @phpstan-ignore notIdentical.alwaysTrue */
            if ( true !== $bst ) {
                return '';
            }
            /** @phpstan-ignore deadCode.unreachable */
            $stVar = $matches[ 1 ];
            $uMaxMatch = 0;
            $stSubst = '';
            foreach ( $i_rVariables as $key => $value ) {
                if ( ! str_starts_with( $stVar, $key ) ) {
                    continue;
                }
                if ( strlen( $key ) <= $uMaxMatch ) {
                    continue;
                }
                $stSubst = $value;
                $uMaxMatch = strlen( $key );
            }
            if ( $uMaxMatch > 0 ) {
                return $stSubst . substr( $stVar, $uMaxMatch );
            }
            $bst = "Undefined variable: {$stVar}";
            return $stVar;
        }, $this->textProcessed );
        /** @phpstan-ignore notIdentical.alwaysTrue */
        if ( $bst !== true ) {
            return $bst;
        }
        /** @phpstan-ignore deadCode.unreachable */
        $this->textProcessed = $st;
        return true;
    }


    /**
     * @param array<string, string> $i_rVariables
     */
    private function substVariablesWithBraces( array $i_rVariables ) : true|string {
        $bst = true;
        $st = preg_replace_callback( '/\$\{([a-zA-Z_][a-zA-Z0-9_]*)}/',
            function ( array $matches ) use ( &$i_rVariables, &$bst ) : string {
                $stVar = $matches[ 1 ];
                if ( array_key_exists( $stVar, $i_rVariables ) ) {
                    return $i_rVariables[ $stVar ];
                }
                /** @phpstan-ignore identical.alwaysFalse */
                if ( true === $bst ) {
                    $bst = "Undefined variable: $stVar";
                }
                return $stVar;
            },
            $this->textProcessed
        );

        /** @phpstan-ignore function.alreadyNarrowedType */
        if ( is_string( $bst ) ) {
            return $bst;
        }

        assert( is_string( $st ) );

        $matches = [];
        preg_match( '/\$\{([a-zA-Z_][a-zA-Z0-9_]*)/', $st, $matches );
        if ( count( $matches ) > 0 ) {
            return 'Unmatched brace in variable substitution';
        }

        $this->textProcessed = $st;
        return true;
    }


}
