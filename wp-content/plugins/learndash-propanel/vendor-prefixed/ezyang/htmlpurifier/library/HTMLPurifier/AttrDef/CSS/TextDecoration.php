<?php

/**
 * Validates the value for the CSS property text-decoration
 * @note This class could be generalized into a version that acts sort of
 *       like Enum except you can compound the allowed values.
 */
class LearnDash_Reports_HTMLPurifier_AttrDef_CSS_TextDecoration extends LearnDash_Reports_HTMLPurifier_AttrDef
{

    /**
     * @param string $string
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        static $allowed_values = array(
            'line-through' => true,
            'overline' => true,
            'underline' => true,
        );

        $string = strtolower($this->parseCDATA($string));

        if ($string === 'none') {
            return $string;
        }

        $parts = explode(' ', $string);
        $final = '';
        foreach ($parts as $part) {
            if (isset($allowed_values[$part])) {
                $final .= $part . ' ';
            }
        }
        $final = rtrim($final);
        if ($final === '') {
            return false;
        }
        return $final;
    }
}

// vim: et sw=4 sts=4
