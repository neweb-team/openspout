<?php

namespace NWT\OpenSpout\Writer\Common\Creator\Style;

use NWT\OpenSpout\Common\Entity\Style\Border;
use NWT\OpenSpout\Common\Entity\Style\CellAlignment;
use NWT\OpenSpout\Common\Entity\Style\Color;
use NWT\OpenSpout\Common\Exception\InvalidArgumentException;
use NWT\OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class StyleBuilderTest extends TestCase
{
    public function testStyleBuilderShouldApplyBorders()
    {
        $border = (new BorderBuilder())
            ->setBorderBottom()
            ->build()
        ;
        $style = (new StyleBuilder())->setBorder($border)->build();
        static::assertTrue($style->shouldApplyBorder());
    }

    public function testStyleBuilderShouldMergeBorders()
    {
        $border = (new BorderBuilder())->setBorderBottom(Color::RED, Border::WIDTH_THIN, Border::STYLE_DASHED)->build();

        $baseStyle = (new StyleBuilder())->setBorder($border)->build();
        $currentStyle = (new StyleBuilder())->build();

        $styleMerger = new StyleMerger();
        $mergedStyle = $styleMerger->merge($currentStyle, $baseStyle);

        static::assertNull($currentStyle->getBorder(), 'Current style has no border');
        static::assertInstanceOf(Border::class, $baseStyle->getBorder(), 'Base style has a border');
        static::assertInstanceOf(Border::class, $mergedStyle->getBorder(), 'Merged style has a border');
    }

    public function testStyleBuilderShouldApplyCellAlignment()
    {
        $style = (new StyleBuilder())->setCellAlignment(CellAlignment::CENTER)->build();
        static::assertTrue($style->shouldApplyCellAlignment());
    }

    public function testStyleBuilderShouldThrowOnInvalidCellAlignment()
    {
        $this->expectException(InvalidArgumentException::class);
        (new StyleBuilder())->setCellAlignment('invalid_cell_alignment')->build();
    }
}
