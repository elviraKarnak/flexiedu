<?php

/**
 * Validates a boolean attribute
 */
class LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Bool extends LearnDash_Reports_HTMLPurifier_AttrDef
{

    /**
     * @type string
     */
    protected $name;

    /**
     * @type bool
     */
    public $minimized = true;

    /**
     * @param bool|string $name
     */
    public function __construct($name = false)
    {
        $this->name = $name;
    }

    /**
     * @param string $string
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->name;
    }

    /**
     * @param string $string Name of attribute
     * @return LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Bool
     */
    public function make($string)
    {
        return new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Bool($string);
    }
}

// vim: et sw=4 sts=4
