<?php

namespace NWT\OpenSpout\Writer\XLSX\Creator;

use NWT\OpenSpout\Common\Helper\Escaper;
use NWT\OpenSpout\Common\Helper\StringHelper;
use NWT\OpenSpout\Common\Manager\OptionsManagerInterface;
use NWT\OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use NWT\OpenSpout\Writer\Common\Entity\Options;
use NWT\OpenSpout\Writer\Common\Helper\ZipHelper;
use NWT\OpenSpout\Writer\XLSX\Helper\FileSystemHelper;

/**
 * Factory for helpers needed by the XLSX Writer.
 */
class HelperFactory extends \NWT\OpenSpout\Common\Creator\HelperFactory
{
    /**
     * @return FileSystemHelper
     */
    public function createSpecificFileSystemHelper(OptionsManagerInterface $optionsManager, InternalEntityFactory $entityFactory)
    {
        $tempFolder = $optionsManager->getOption(Options::TEMP_FOLDER);
        $zipHelper = $this->createZipHelper($entityFactory);
        $escaper = $this->createStringsEscaper();

        return new FileSystemHelper($tempFolder, $zipHelper, $escaper);
    }

    /**
     * @return Escaper\XLSX
     */
    public function createStringsEscaper()
    {
        return new Escaper\XLSX();
    }

    /**
     * @return StringHelper
     */
    public function createStringHelper()
    {
        return new StringHelper();
    }

    /**
     * @return ZipHelper
     */
    private function createZipHelper(InternalEntityFactory $entityFactory)
    {
        return new ZipHelper($entityFactory);
    }
}
