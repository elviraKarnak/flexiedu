<?php

/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class LearnDash_Reports_HTMLPurifier_AttrTransform_Border extends LearnDash_Reports_HTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['border'])) {
            return $attr;
        }
        $border_width = $this->confiscateAttr($attr, 'border');
        // some validation should happen here
        $this->prependCSS($attr, "border:{$border_width}px solid;");
        return $attr;
    }
}

// vim: et sw=4 sts=4
