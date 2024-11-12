<?php

namespace Spout\Writer\Common\Manager;

use NWT\OpenSpout\Common\Entity\Cell;
use NWT\OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use NWT\OpenSpout\Writer\Common\Manager\CellManager;
use NWT\OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CellManagerTest extends TestCase
{
    public function testApplyStyle()
    {
        $cellManager = new CellManager(new StyleMerger());
        $cell = new Cell('test');

        static::assertFalse($cell->getStyle()->isFontBold());

        $style = (new StyleBuilder())->setFontBold()->build();
        $cellManager->applyStyle($cell, $style);

        static::assertTrue($cell->getStyle()->isFontBold());
    }
}
