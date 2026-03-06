<?php

class LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_Proprietary extends LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy
{

    /**
     * @type string
     */
    public $name = 'Tidy_Proprietary';

    /**
     * @type string
     */
    public $defaultLevel = 'light';

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = array();
        $r['table@background'] = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['td@background']    = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['th@background']    = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['tr@background']    = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['thead@background'] = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['tfoot@background'] = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['tbody@background'] = new LearnDash_Reports_HTMLPurifier_AttrTransform_Background();
        $r['table@height']     = new LearnDash_Reports_HTMLPurifier_AttrTransform_Length('height');
        return $r;
    }
}

// vim: et sw=4 sts=4
