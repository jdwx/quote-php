<?php


declare( strict_types = 1 );


namespace JDWX\Quote;


enum Segment {


    case UNDEFINED;


    case DELIMITER;

    /** Simple, unquoted text. All substitutions and escapes apply. */
    case UNQUOTED;

    /** Hard quotes prevent escaping anything but the quote character. */
    case HARD_QUOTED;

    /**
     * Soft quotes prevent splitting on delimiters and disable other types of
     * quotes. All other escapes and substitutions are still processed.
     */
    case SOFT_QUOTED;

    /** Callback quotes trigger callbacks with the quoted content, after handling other processing. */
    case CALLBACK_QUOTED;

    case COMMENT;


}

