<?php

/**
 * Validates nntp (Network News Transfer Protocol) as defined by generic RFC 1738
 */
class LearnDash_Reports_HTMLPurifier_URIScheme_nntp extends LearnDash_Reports_HTMLPurifier_URIScheme
{
    /**
     * @type int
     */
    public $default_port = 119;

    /**
     * @type bool
     */
    public $browsable = false;

    /**
     * @param LearnDash_Reports_HTMLPurifier_URI $uri
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool
     */
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = null;
        $uri->query = null;
        return true;
    }
}

// vim: et sw=4 sts=4
