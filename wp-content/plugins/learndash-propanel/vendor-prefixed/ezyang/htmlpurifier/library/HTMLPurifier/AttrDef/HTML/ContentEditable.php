<?php

class LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ContentEditable extends LearnDash_Reports_HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $allowed = array('false');
        if ($config->get('HTML.Trusted')) {
            $allowed = array('', 'true', 'false');
        }

        $enum = new LearnDash_Reports_HTMLPurifier_AttrDef_Enum($allowed);

        return $enum->validate($string, $config, $context);
    }
}
