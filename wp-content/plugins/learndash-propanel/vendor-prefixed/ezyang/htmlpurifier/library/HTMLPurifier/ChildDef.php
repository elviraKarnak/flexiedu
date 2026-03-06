<?php

/**
 * Defines allowed child nodes and validates nodes against it.
 */
abstract class LearnDash_Reports_HTMLPurifier_ChildDef
{
    /**
     * Type of child definition, usually right-most part of class name lowercase.
     * Used occasionally in terms of context.
     * @type string
     */
    public $type;

    /**
     * Indicates whether or not an empty array of children is okay.
     *
     * This is necessary for redundant checking when changes affecting
     * a child node may cause a parent node to now be disallowed.
     * @type bool
     */
    public $allow_empty;

    /**
     * Lookup array of all elements that this definition could possibly allow.
     * @type array
     */
    public $elements = array();

    /**
     * Get lookup of tag names that should not close this element automatically.
     * All other elements will do so.
     * @param LearnDash_Reports_HTMLPurifier_Config $config LearnDash_Reports_HTMLPurifier_Config object
     * @return array
     */
    public function getAllowedElements($config)
    {
        return $this->elements;
    }

    /**
     * Validates nodes according to definition and returns modification.
     *
     * @param LearnDash_Reports_HTMLPurifier_Node[] $children Array of LearnDash_Reports_HTMLPurifier_Node
     * @param LearnDash_Reports_HTMLPurifier_Config $config LearnDash_Reports_HTMLPurifier_Config object
     * @param LearnDash_Reports_HTMLPurifier_Context $context LearnDash_Reports_HTMLPurifier_Context object
     * @return bool|array true to leave nodes as is, false to remove parent node, array of replacement children
     */
    abstract public function validateChildren($children, $config, $context);
}

// vim: et sw=4 sts=4
