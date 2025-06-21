<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


enum SegmentType {


    /** Undefined text could be anything or a series of anything. */
    case UNDEFINED;


    /**
     * Comments treat their contents as a single non-printing literal. They are replaced with
     * an empty string in the output.
     */
    case COMMENT;

    /** Hard quotes unescape escaped close-quotes but otherwise treat their contents as
     * a single printing literal. They are replaced with their contents minus the quotes
     * in the output.
     */
    case HARD_QUOTED;

    /**
     * Soft quotes allow literals, escaping, delimiters, and callbacks.
     * They are replaced with their contents minus the quotes in the output.
     */
    case SOFT_QUOTED;

    /**
     * Strong callbacks subprocess hard quotes, literals, escaping, delimiters, and
     * weak or open callbacks into a new string. They are replaced with the results
     * of a callback. Strong callbacks roughly correspond to
     * `backticks` or $(execution) in shell scripts.
     */
    case STRONG_CALLBACK;

    /**
     * Weak callbacks subprocess literals and delimiters into a new string. They are replaced
     * with the results of a callback with that string as a parameter.
     * Weak callbacks roughly correspond to ${variables} in shell scripts.
     */
    case WEAK_CALLBACK;

    /**
     * Open callbacks allow literals. They are replaced with the results of a callback with
     * their value as a parameter. Open callbacks roughly correspond
     * to $variables in shell scripts.
     */
    case OPEN_CALLBACK;

    /**
     * Escapes are used to escape special characters in the text. They are replaced with their
     * unescaped contents in the output.
     */
    case ESCAPE;

    /**
     * Delimiters are used to separate literals. Adjacent delimiters are coalesced.
     * They are replaced with their contents in the output.
     * (Whitespace is frequently used as a delimiter.)
     */
    case DELIMITER;

    /**
     * Literals are anything that is not a comment, quote, escape, delimiter, or callback.
     * Adjacent literals are coalesced. They are replaced with their contents in the output.
     */
    case LITERAL;

    /** Simple, unquoted text. All substitutions and escapes apply. This should be obsolete. */
    case UNQUOTED;


    public function allowComments() : bool {
        return $this === self::UNDEFINED;
    }


    public function allowDelimiters() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::SOFT_QUOTED, self::STRONG_CALLBACK,
            self::WEAK_CALLBACK => true,
            default => false,
        };
    }


    public function allowEscapes() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::SOFT_QUOTED, self::STRONG_CALLBACK => true,
            default => false,
        };
    }


    public function allowHardQuotes() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::STRONG_CALLBACK => true,
            default => false,
        };
    }


    public function allowOpenCallbacks() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::SOFT_QUOTED, self::STRONG_CALLBACK => true,
            default => false,
        };
    }


    public function allowSoftQuotes() : bool {
        return $this === self::UNDEFINED;
    }


    public function allowStrongCallbacks() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::SOFT_QUOTED => true,
            default => false,
        };
    }


    public function allowWeakCallbacks() : bool {
        return match ( $this ) {
            self::UNDEFINED, self::SOFT_QUOTED, self::STRONG_CALLBACK => true,
            default => false,
        };
    }


    public function canCoalesce() : bool {
        return match ( $this ) {
            self::DELIMITER, self::LITERAL, self::UNDEFINED => true,
            default => false,
        };
    }


}

