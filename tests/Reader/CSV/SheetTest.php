<?php

namespace NWT\OpenSpout\Reader\CSV;

use NWT\OpenSpout\Reader\Common\Creator\ReaderEntityFactory;
use NWT\OpenSpout\Reader\SheetInterface;
use NWT\OpenSpout\TestUsingResource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SheetTest extends TestCase
{
    use TestUsingResource;

    public function testReaderShouldReturnCorrectSheetInfos()
    {
        $sheet = $this->openFileAndReturnSheet('csv_standard.csv');

        static::assertSame('', $sheet->getName());
        static::assertSame(0, $sheet->getIndex());
        static::assertTrue($sheet->isActive());
    }

    /**
     * @param string $fileName
     *
     * @return SheetInterface
     */
    private function openFileAndReturnSheet($fileName)
    {
        $resourcePath = $this->getResourcePath($fileName);
        $reader = ReaderEntityFactory::createCSVReader();
        $reader->open($resourcePath);

        $sheet = $reader->getSheetIterator()->current();

        $reader->close();

        return $sheet;
    }
}
