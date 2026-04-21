<?php

namespace LearnDash\Gradebook\SSNepenthe\ColorUtils\Transformers;

use LearnDash\Gradebook\SSNepenthe\ColorUtils\Colors\Color;

/**
 * Interface TransformerInterface
 */
interface TransformerInterface
{
    /**
     * @param Color $color
     * @return Color
     */
    public function transform(Color $color) : Color;
}
