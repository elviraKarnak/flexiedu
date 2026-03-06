<?php

/**
 * Concrete end token class.
 *
 * @warning This class accepts attributes even though end tags cannot. This
 * is for optimization reasons, as under normal circumstances, the Lexers
 * do not pass attributes.
 */
class LearnDash_Reports_HTMLPurifier_Token_End extends LearnDash_Reports_HTMLPurifier_Token_Tag
{
    /**
     * Token that started this node.
     * Added by MakeWellFormed. Please do not edit this!
     * @type LearnDash_Reports_HTMLPurifier_Token
     */
    public $start;

    public function toNode() {
        throw new Exception("LearnDash_Reports_HTMLPurifier_Token_End->toNode not supported!");
    }
}

// vim: et sw=4 sts=4
