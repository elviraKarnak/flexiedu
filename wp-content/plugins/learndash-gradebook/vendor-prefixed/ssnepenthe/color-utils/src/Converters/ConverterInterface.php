<?php

namespace LearnDash\Gradebook\SSNepenthe\ColorUtils\Converters;

use LearnDash\Gradebook\SSNepenthe\ColorUtils\Colors\ColorInterface;

/**
 * Interface ConverterInterface
 */
interface ConverterInterface
{
    /**
     * @param ColorInterface $color
     * @return ColorInterface
     */
    public function convert(ColorInterface $color) : ColorInterface;
}
