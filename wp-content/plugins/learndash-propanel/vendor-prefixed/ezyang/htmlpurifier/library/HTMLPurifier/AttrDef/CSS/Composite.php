<?php

/**
 * Allows multiple validators to attempt to validate attribute.
 *
 * Composite is just what it sounds like: a composite of many validators.
 * This means that multiple LearnDash_Reports_HTMLPurifier_AttrDef objects will have a whack
 * at the string.  If one of them passes, that's what is returned.  This is
 * especially useful for CSS values, which often are a choice between
 * an enumerated set of predefined values or a flexible data type.
 */
class LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Composite extends LearnDash_Reports_HTMLPurifier_AttrDef
{

    /**
     * List of objects that may process strings.
     * @type LearnDash_Reports_HTMLPurifier_AttrDef[]
     * @todo Make protected
     */
    public $defs;

    /**
     * @param LearnDash_Reports_HTMLPurifier_AttrDef[] $defs List of LearnDash_Reports_HTMLPurifier_AttrDef objects
     */
    public function __construct($defs)
    {
        $this->defs = $defs;
    }

    /**
     * @param string $string
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        foreach ($this->defs as $i => $def) {
            $result = $this->defs[$i]->validate($string, $config, $context);
            if ($result !== false) {
                return $result;
            }
        }
        return false;
    }
}

// vim: et sw=4 sts=4
