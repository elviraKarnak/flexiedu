<?php

declare(strict_types=1);

namespace LearnDash\Reports\ZipStream\Exception;

use LearnDash\Reports\ZipStream\Exception;

/**
 * This Exception gets invoked if `fread` fails on a stream.
 */
class StreamNotReadableException extends Exception
{
    /**
     * Constructor of the Exception
     *
     * @param string $fileName - The name of the file which the stream belongs to.
     */
    public function __construct(string $fileName)
    {
        parent::__construct("The stream for $fileName could not be read.");
    }
}
