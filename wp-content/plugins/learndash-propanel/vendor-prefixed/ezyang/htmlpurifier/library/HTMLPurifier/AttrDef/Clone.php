<?php

/**
 * Dummy AttrDef that mimics another AttrDef, BUT it generates clones
 * with make.
 */
class LearnDash_Reports_HTMLPurifier_AttrDef_Clone extends LearnDash_Reports_HTMLPurifier_AttrDef
{
    /**
     * What we're cloning.
     * @type LearnDash_Reports_HTMLPurifier_AttrDef
     */
    protected $clone;

    /**
     * @param LearnDash_Reports_HTMLPurifier_AttrDef $clone
     */
    public function __construct($clone)
    {
        $this->clone = $clone;
    }

    /**
     * @param string $v
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($v, $config, $context)
    {
        return $this->clone->validate($v, $config, $context);
    }

    /**
     * @param string $string
     * @return LearnDash_Reports_HTMLPurifier_AttrDef
     */
    public function make($string)
    {
        return clone $this->clone;
    }
}

// vim: et sw=4 sts=4
