<?php

namespace NWT\OpenSpout\Common\Helper;

use NWT\OpenSpout\Common\Exception\EncodingConversionException;
use NWT\OpenSpout\TestUsingResource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class EncodingHelperTest extends TestCase
{
    use TestUsingResource;

    /**
     * @return array
     */
    public function dataProviderForTestGetBytesOffsetToSkipBOM()
    {
        return [
            ['csv_with_utf8_bom.csv', EncodingHelper::ENCODING_UTF8, 3],
            ['csv_with_utf16be_bom.csv', EncodingHelper::ENCODING_UTF16_BE, 2],
            ['csv_with_utf32le_bom.csv', EncodingHelper::ENCODING_UTF32_LE, 4],
            ['csv_with_encoding_utf16le_no_bom.csv', EncodingHelper::ENCODING_UTF16_LE, 0],
            ['csv_standard.csv', EncodingHelper::ENCODING_UTF8, 0],
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetBytesOffsetToSkipBOM
     *
     * @param string $fileName
     * @param string $encoding
     * @param int    $expectedBytesOffset
     */
    public function testGetBytesOffsetToSkipBOM($fileName, $encoding, $expectedBytesOffset)
    {
        $resourcePath = $this->getResourcePath($fileName);
        $filePointer = fopen($resourcePath, 'r');

        $encodingHelper = new EncodingHelper(new GlobalFunctionsHelper());
        $bytesOffset = $encodingHelper->getBytesOffsetToSkipBOM($filePointer, $encoding);

        static::assertSame($expectedBytesOffset, $bytesOffset);
    }

    /**
     * @return array
     */
    public function dataProviderForIconvOrMbstringUsage()
    {
        return [
            [$shouldUseIconv = true],
            [$shouldNotUseIconv = false],
        ];
    }

    /**
     * @dataProvider dataProviderForIconvOrMbstringUsage
     *
     * @param bool $shouldUseIconv
     */
    public function testAttemptConversionToUTF8ShouldThrowIfConversionFailed($shouldUseIconv)
    {
        $this->expectException(EncodingConversionException::class);

        $helperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\GlobalFunctionsHelper')
            ->onlyMethods(['iconv', 'mb_convert_encoding'])
            ->getMock()
        ;
        $helperStub->method('iconv')->willReturn(false);
        $helperStub->method('mb_convert_encoding')->willReturn(false);

        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->setConstructorArgs([$helperStub])
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn($shouldUseIconv);
        $encodingHelperStub->method('canUseMbString')->willReturn(true);

        $encodingHelperStub->attemptConversionToUTF8('input', EncodingHelper::ENCODING_UTF16_LE);
    }

    public function testAttemptConversionToUTF8ShouldThrowIfConversionNotSupported()
    {
        $this->expectException(EncodingConversionException::class);

        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->disableOriginalConstructor()
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn(false);
        $encodingHelperStub->method('canUseMbString')->willReturn(false);

        $encodingHelperStub->attemptConversionToUTF8('input', EncodingHelper::ENCODING_UTF16_LE);
    }

    /**
     * @dataProvider dataProviderForIconvOrMbstringUsage
     *
     * @param bool $shouldUseIconv
     */
    public function testAttemptConversionToUTF8ShouldReturnReencodedString($shouldUseIconv)
    {
        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->setConstructorArgs([new GlobalFunctionsHelper()])
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn($shouldUseIconv);
        $encodingHelperStub->method('canUseMbString')->willReturn(true);

        $encodedString = iconv(EncodingHelper::ENCODING_UTF8, EncodingHelper::ENCODING_UTF16_LE, 'input');
        $decodedString = $encodingHelperStub->attemptConversionToUTF8($encodedString, EncodingHelper::ENCODING_UTF16_LE);

        static::assertSame('input', $decodedString);
    }

    public function testAttemptConversionToUTF8ShouldBeNoopWhenTargetIsUTF8()
    {
        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->disableOriginalConstructor()
            ->onlyMethods(['canUseIconv'])
            ->getMock()
        ;
        $encodingHelperStub->expects(static::never())->method('canUseIconv');

        $decodedString = $encodingHelperStub->attemptConversionToUTF8('input', EncodingHelper::ENCODING_UTF8);
        static::assertSame('input', $decodedString);
    }

    /**
     * @dataProvider dataProviderForIconvOrMbstringUsage
     *
     * @param bool $shouldUseIconv
     */
    public function testAttemptConversionFromUTF8ShouldThrowIfConversionFailed($shouldUseIconv)
    {
        $this->expectException(EncodingConversionException::class);

        $helperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\GlobalFunctionsHelper')
            ->onlyMethods(['iconv', 'mb_convert_encoding'])
            ->getMock()
        ;
        $helperStub->method('iconv')->willReturn(false);
        $helperStub->method('mb_convert_encoding')->willReturn(false);

        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->setConstructorArgs([$helperStub])
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn($shouldUseIconv);
        $encodingHelperStub->method('canUseMbString')->willReturn(true);

        $encodingHelperStub->attemptConversionFromUTF8('input', EncodingHelper::ENCODING_UTF16_LE);
    }

    public function testAttemptConversionFromUTF8ShouldThrowIfConversionNotSupported()
    {
        $this->expectException(EncodingConversionException::class);

        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->disableOriginalConstructor()
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn(false);
        $encodingHelperStub->method('canUseMbString')->willReturn(false);

        $encodingHelperStub->attemptConversionFromUTF8('input', EncodingHelper::ENCODING_UTF16_LE);
    }

    /**
     * @dataProvider dataProviderForIconvOrMbstringUsage
     *
     * @param bool $shouldUseIconv
     */
    public function testAttemptConversionFromUTF8ShouldReturnReencodedString($shouldUseIconv)
    {
        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->setConstructorArgs([new GlobalFunctionsHelper()])
            ->onlyMethods(['canUseIconv', 'canUseMbString'])
            ->getMock()
        ;
        $encodingHelperStub->method('canUseIconv')->willReturn($shouldUseIconv);
        $encodingHelperStub->method('canUseMbString')->willReturn(true);

        $encodedString = $encodingHelperStub->attemptConversionFromUTF8('input', EncodingHelper::ENCODING_UTF16_LE);
        $encodedStringWithIconv = iconv(EncodingHelper::ENCODING_UTF8, EncodingHelper::ENCODING_UTF16_LE, 'input');

        static::assertSame($encodedStringWithIconv, $encodedString);
    }

    public function testAttemptConversionFromUTF8ShouldBeNoopWhenTargetIsUTF8()
    {
        /** @var EncodingHelper|\PHPUnit\Framework\MockObject\MockObject $encodingHelperStub */
        $encodingHelperStub = $this->getMockBuilder('\NWT\OpenSpout\Common\Helper\EncodingHelper')
            ->disableOriginalConstructor()
            ->onlyMethods(['canUseIconv'])
            ->getMock()
        ;
        $encodingHelperStub->expects(static::never())->method('canUseIconv');

        $encodedString = $encodingHelperStub->attemptConversionFromUTF8('input', EncodingHelper::ENCODING_UTF8);
        static::assertSame('input', $encodedString);
    }
}
