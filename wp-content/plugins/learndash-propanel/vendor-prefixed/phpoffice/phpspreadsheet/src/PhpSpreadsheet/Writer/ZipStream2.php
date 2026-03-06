<?php

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Writer;

use LearnDash\Reports\ZipStream\Option\Archive;
use LearnDash\Reports\ZipStream\ZipStream;

class ZipStream2
{
    /**
     * @param resource $fileHandle
     */
    public static function newZipStream($fileHandle): ZipStream
    {
        $options = new Archive();
        $options->setEnableZip64(false);
        $options->setOutputStream($fileHandle);

        return new ZipStream(null, $options);
    }
}
