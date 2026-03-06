<?php

/**
 * Concrete comment node class.
 */
class LearnDash_Reports_HTMLPurifier_Node_Comment extends LearnDash_Reports_HTMLPurifier_Node
{
    /**
     * Character data within comment.
     * @type string
     */
    public $data;

    /**
     * @type bool
     */
    public $is_whitespace = true;

    /**
     * Transparent constructor.
     *
     * @param string $data String comment data.
     * @param int $line
     * @param int $col
     */
    public function __construct($data, $line = null, $col = null)
    {
        $this->data = $data;
        $this->line = $line;
        $this->col = $col;
    }

    public function toTokenPair() {
        return array(new LearnDash_Reports_HTMLPurifier_Token_Comment($this->data, $this->line, $this->col), null);
    }
}
