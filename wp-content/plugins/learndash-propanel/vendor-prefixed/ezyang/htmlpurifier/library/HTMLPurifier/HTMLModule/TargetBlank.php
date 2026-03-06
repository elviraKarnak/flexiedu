<?php

/**
 * Module adds the target=blank attribute transformation to a tags.  It
 * is enabled by HTML.TargetBlank
 */
class LearnDash_Reports_HTMLPurifier_HTMLModule_TargetBlank extends LearnDash_Reports_HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetBlank';

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new LearnDash_Reports_HTMLPurifier_AttrTransform_TargetBlank();
    }
}

// vim: et sw=4 sts=4
