<?php

/**
 * Module adds the target-based noreferrer attribute transformation to a tags.  It
 * is enabled by HTML.TargetNoreferrer
 */
class LearnDash_Reports_HTMLPurifier_HTMLModule_TargetNoreferrer extends LearnDash_Reports_HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetNoreferrer';

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     */
    public function setup($config) {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new LearnDash_Reports_HTMLPurifier_AttrTransform_TargetNoreferrer();
    }
}
