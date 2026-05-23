<?php


declare( strict_types = 1 );


namespace JDWX\Quote\Operators;


use JDWX\Quote\Piece;


abstract class AbstractOperator implements OperatorInterface {


    /**
     * Walk through the string, checking each character for matches
     * with this operator.
     *
     * @return string Returns the processed string with replacements applied.
     */
    public function __invoke( string $i_st ) : string {
        $stOut = '';
        while ( $i_st !== '' ) {
            $match = $this->match( $i_st );
            if ( $match === null ) {
                $stOut .= substr( $i_st, 0, 1 );
                $i_st = substr( $i_st, 1 );
                continue;
            }
            $stOut .= $match->stReplace;
            $i_st = $match->stRest;
        }
        return $stOut;
    }


    /**
     * Generate a Piece object from the match, replacement, and remaining text.
     *
     * @param string $i_stMatch   The literal substring that was consumed.
     * @param string $i_stReplace The replacement value for the match.
     * @param string $i_stRest    The remaining text after the match.
     * @return Piece Returns a Piece object representing the match and its replacement.
     */
    protected function makePiece( string $i_stMatch, string $i_stReplace, string $i_stRest ) : Piece {
        return new Piece( $i_stMatch, $i_stReplace, $i_stRest );
    }


    /**
     * Given the matched text (e.g., the variable name), return the replacement string
     * (e.g., the variable value). This is called from result() with the text of the
     * match. If it returns null, no substitution occurs.
     *
     * @param string $i_stMatch The matched text to be replaced.
     * @return string|null The replacement text to use, or null if no replacement is possible.
     */
    abstract protected function replace( string $i_stMatch ) : ?string;


    /**
     * Processes the match and text to generate a Piece object or return null if
     * replacement is not possible. This handles splitting out the remaining text.
     * This is a helper function that can be called from match() to do the work
     * of computing the replacement and remaining text.
     *
     * @param string $i_stMatch The matched substring to be processed.
     * @param string $i_stText  The full text containing the match.
     * @return Piece|null Returns a Piece object if the replacement is valid, or null otherwise.
     */
    protected function result( string $i_stMatch, string $i_stText ) : ?Piece {
        $nstReplace = $this->replace( $i_stMatch );
        if ( ! is_string( $nstReplace ) ) {
            return null;
        }
        $stRest = substr( $i_stText, strlen( $i_stMatch ) );
        return $this->makePiece( $i_stMatch, $nstReplace, $stRest );
    }


}
