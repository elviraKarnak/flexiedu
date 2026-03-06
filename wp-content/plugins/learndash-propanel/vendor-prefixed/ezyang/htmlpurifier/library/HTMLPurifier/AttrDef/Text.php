<?php

/**
 * Validates arbitrary text according to the HTML spec.
 */
class LearnDash_Reports_HTMLPurifier_AttrDef_Text extends LearnDash_Reports_HTMLPurifier_AttrDef
{

    /**
     * @param string $string
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->parseCDATA($string);
    }
}

// vim: et sw=4 sts=4
