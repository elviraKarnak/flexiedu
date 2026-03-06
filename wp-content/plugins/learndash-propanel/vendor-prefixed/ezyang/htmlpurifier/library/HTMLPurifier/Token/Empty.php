<?php

/**
 * Concrete empty token class.
 */
class LearnDash_Reports_HTMLPurifier_Token_Empty extends LearnDash_Reports_HTMLPurifier_Token_Tag
{
    public function toNode() {
        $n = parent::toNode();
        $n->empty = true;
        return $n;
    }
}

// vim: et sw=4 sts=4
