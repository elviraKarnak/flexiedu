<?php

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Collection;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Settings;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class CellsFactory
{
    /**
     * Initialise the cache storage.
     *
     * @param Worksheet $worksheet Enable cell caching for this worksheet
     *
     * */
    public static function getInstance(Worksheet $worksheet): Cells
    {
        return new Cells($worksheet, Settings::getCache());
    }
}
