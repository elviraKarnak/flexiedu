<?php

namespace LearnDash\Gradebook\SSNepenthe\ColorUtils\Transformers;

use LearnDash\Gradebook\SSNepenthe\ColorUtils\Colors\Color;

/**
 * Class AdjustHue
 */
class AdjustHue implements TransformerInterface
{
    /**
     * @var AdjustColor
     */
    protected $transformer;

    /**
     * @param float $amount
     */
    public function __construct(float $amount)
    {
        $this->transformer = new AdjustColor(['hue' => $amount]);
    }

    /**
     * @param Color $color
     * @return Color
     */
    public function transform(Color $color) : Color
    {
        return $this->transformer->transform($color);
    }
}
