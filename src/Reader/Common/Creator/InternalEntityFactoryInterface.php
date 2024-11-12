<?php

namespace NWT\OpenSpout\Reader\Common\Creator;

use NWT\OpenSpout\Common\Entity\Cell;
use NWT\OpenSpout\Common\Entity\Row;

/**
 * Interface EntityFactoryInterface.
 */
interface InternalEntityFactoryInterface
{
    /**
     * @param Cell[] $cells
     *
     * @return Row
     */
    public function createRow(array $cells = []);

    /**
     * @param mixed $cellValue
     *
     * @return Cell
     */
    public function createCell($cellValue);
}
