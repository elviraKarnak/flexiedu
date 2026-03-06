<?php

class LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator extends LearnDash_Reports_HTMLPurifier_DefinitionCache
{

    /**
     * Cache object we are decorating
     * @type LearnDash_Reports_HTMLPurifier_DefinitionCache
     */
    public $cache;

    /**
     * The name of the decorator
     * @var string
     */
    public $name;

    public function __construct()
    {
    }

    /**
     * Lazy decorator function
     * @param LearnDash_Reports_HTMLPurifier_DefinitionCache $cache Reference to cache object to decorate
     * @return LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator
     */
    public function decorate(&$cache)
    {
        $decorator = $this->copy();
        // reference is necessary for mocks in PHP 4
        $decorator->cache =& $cache;
        $decorator->type = $cache->type;
        return $decorator;
    }

    /**
     * Cross-compatible clone substitute
     * @return LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator
     */
    public function copy()
    {
        return new LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator();
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function add($def, $config)
    {
        return $this->cache->add($def, $config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function set($def, $config)
    {
        return $this->cache->set($def, $config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function replace($def, $config)
    {
        return $this->cache->replace($def, $config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function get($config)
    {
        return $this->cache->get($config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function remove($config)
    {
        return $this->cache->remove($config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function flush($config)
    {
        return $this->cache->flush($config);
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return mixed
     */
    public function cleanup($config)
    {
        return $this->cache->cleanup($config);
    }
}

// vim: et sw=4 sts=4
