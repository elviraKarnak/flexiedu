<?php

/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 */
class LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator_Cleanup extends LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator
{
    /**
     * @type string
     */
    public $name = 'Cleanup';

    /**
     * @return LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator_Cleanup
     */
    public function copy()
    {
        return new LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator_Cleanup();
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function set($def, $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function replace($def, $config)
    {
        $status = parent::replace($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function get($config)
    {
        $ret = parent::get($config);
        if (!$ret) {
            parent::cleanup($config);
        }
        return $ret;
    }
}

// vim: et sw=4 sts=4
