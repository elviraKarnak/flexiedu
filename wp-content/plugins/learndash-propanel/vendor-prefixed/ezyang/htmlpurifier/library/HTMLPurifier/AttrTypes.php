<?php

/**
 * Provides lookup array of attribute types to LearnDash_Reports_HTMLPurifier_AttrDef objects
 */
class LearnDash_Reports_HTMLPurifier_AttrTypes
{
    /**
     * Lookup array of attribute string identifiers to concrete implementations.
     * @type LearnDash_Reports_HTMLPurifier_AttrDef[]
     */
    protected $info = array();

    /**
     * Constructs the info array, supplying default implementations for attribute
     * types.
     */
    public function __construct()
    {
        // XXX This is kind of poor, since we don't actually /clone/
        // instances; instead, we use the supplied make() attribute. So,
        // the underlying class must know how to deal with arguments.
        // With the old implementation of Enum, that ignored its
        // arguments when handling a make dispatch, the IAlign
        // definition wouldn't work.

        // pseudo-types, must be instantiated via shorthand
        $this->info['Enum']    = new LearnDash_Reports_HTMLPurifier_AttrDef_Enum();
        $this->info['Bool']    = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Bool();

        $this->info['CDATA']    = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();
        $this->info['ID']       = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ID();
        $this->info['Length']   = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Length();
        $this->info['MultiLength'] = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_MultiLength();
        $this->info['NMTOKENS'] = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Nmtokens();
        $this->info['Pixels']   = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Pixels();
        $this->info['Text']     = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();
        $this->info['URI']      = new LearnDash_Reports_HTMLPurifier_AttrDef_URI();
        $this->info['LanguageCode'] = new LearnDash_Reports_HTMLPurifier_AttrDef_Lang();
        $this->info['Color']    = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Color();
        $this->info['IAlign']   = self::makeEnum('top,middle,bottom,left,right');
        $this->info['LAlign']   = self::makeEnum('top,bottom,left,right');
        $this->info['FrameTarget'] = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_FrameTarget();
        $this->info['ContentEditable'] = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ContentEditable();

        // unimplemented aliases
        $this->info['ContentType'] = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();
        $this->info['ContentTypes'] = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();
        $this->info['Charsets'] = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();
        $this->info['Character'] = new LearnDash_Reports_HTMLPurifier_AttrDef_Text();

        // "proprietary" types
        $this->info['Class'] = new LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Class();

        // number is really a positive integer (one or more digits)
        // FIXME: ^^ not always, see start and value of list items
        $this->info['Number']   = new LearnDash_Reports_HTMLPurifier_AttrDef_Integer(false, false, true);
    }

    private static function makeEnum($in)
    {
        return new LearnDash_Reports_HTMLPurifier_AttrDef_Clone(new LearnDash_Reports_HTMLPurifier_AttrDef_Enum(explode(',', $in)));
    }

    /**
     * Retrieves a type
     * @param string $type String type name
     * @return LearnDash_Reports_HTMLPurifier_AttrDef Object AttrDef for type
     */
    public function get($type)
    {
        // determine if there is any extra info tacked on
        if (strpos($type, '#') !== false) {
            list($type, $string) = explode('#', $type, 2);
        } else {
            $string = '';
        }

        if (!isset($this->info[$type])) {
            throw new Exception('Cannot retrieve undefined attribute type ' . $type);
            return;
        }
        return $this->info[$type]->make($string);
    }

    /**
     * Sets a new implementation for a type
     * @param string $type String type name
     * @param LearnDash_Reports_HTMLPurifier_AttrDef $impl Object AttrDef for type
     */
    public function set($type, $impl)
    {
        $this->info[$type] = $impl;
    }
}

// vim: et sw=4 sts=4
