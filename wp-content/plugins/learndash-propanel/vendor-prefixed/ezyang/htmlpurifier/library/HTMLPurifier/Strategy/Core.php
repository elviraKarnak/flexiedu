<?php

/**
 * Core strategy composed of the big four strategies.
 */
class LearnDash_Reports_HTMLPurifier_Strategy_Core extends LearnDash_Reports_HTMLPurifier_Strategy_Composite
{
    public function __construct()
    {
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_ValidateAttributes();
    }
}

// vim: et sw=4 sts=4
