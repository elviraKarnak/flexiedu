<?php

// this MUST be placed in post, as it assumes that any value in dir is valid

/**
 * Post-transform that ensures that bdo tags have the dir attribute set.
 */
class LearnDash_Reports_HTMLPurifier_AttrTransform_BdoDir extends LearnDash_Reports_HTMLPurifier_AttrTransform
{

    /**
     * @param array $attr
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (isset($attr['dir'])) {
            return $attr;
        }
        $attr['dir'] = $config->get('Attr.DefaultTextDir');
        return $attr;
    }
}

// vim: et sw=4 sts=4
