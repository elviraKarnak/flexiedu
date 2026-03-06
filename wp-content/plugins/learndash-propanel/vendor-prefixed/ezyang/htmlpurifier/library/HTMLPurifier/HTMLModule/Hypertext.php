<?php

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class LearnDash_Reports_HTMLPurifier_HTMLModule_Hypertext extends LearnDash_Reports_HTMLPurifier_HTMLModule
{

    /**
     * @type string
     */
    public $name = 'Hypertext';

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $a = $this->addElement(
            'a',
            'Inline',
            'Inline',
            'Common',
            array(
                // 'accesskey' => 'Character',
                // 'charset' => 'Charset',
                'href' => 'URI',
                // 'hreflang' => 'LanguageCode',
                'rel' => new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_LinkTypes('rel'),
                'rev' => new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_LinkTypes('rev'),
                // 'tabindex' => 'Number',
                // 'type' => 'ContentType',
            )
        );
        $a->formatting = true;
        $a->excludes = array('a' => true);
    }
}

// vim: et sw=4 sts=4
