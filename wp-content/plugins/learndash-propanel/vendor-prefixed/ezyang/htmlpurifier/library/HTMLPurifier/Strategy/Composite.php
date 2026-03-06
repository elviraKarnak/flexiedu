<?php

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
abstract class LearnDash_Reports_HTMLPurifier_Strategy_Composite extends LearnDash_Reports_HTMLPurifier_Strategy
{

    /**
     * List of strategies to run tokens through.
     * @type LearnDash_Reports_HTMLPurifier_Strategy[]
     */
    protected $strategies = array();

    /**
     * @param LearnDash_Reports_HTMLPurifier_Token[] $tokens
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return LearnDash_Reports_HTMLPurifier_Token[]
     */
    public function execute($tokens, $config, $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }
        return $tokens;
    }
}

// vim: et sw=4 sts=4
