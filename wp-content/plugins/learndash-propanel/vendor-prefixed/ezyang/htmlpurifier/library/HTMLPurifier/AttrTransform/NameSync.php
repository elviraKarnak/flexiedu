<?php

/**
 * Post-transform that performs validation to the name attribute; if
 * it is present with an equivalent id attribute, it is passed through;
 * otherwise validation is performed.
 */
class LearnDash_Reports_HTMLPurifier_AttrTransform_NameSync extends LearnDash_Reports_HTMLPurifier_AttrTransform
{

    /**
     * @type LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ID
     */
    public $idDef;

    public function __construct()
    {
        $this->idDef = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ID();
    }

    /**
     * @param array $attr
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['name'])) {
            return $attr;
        }
        $name = $attr['name'];
        if (isset($attr['id']) && $attr['id'] === $name) {
            return $attr;
        }
        $result = $this->idDef->validate($name, $config, $context);
        if ($result === false) {
            unset($attr['name']);
        } else {
            $attr['name'] = $result;
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
