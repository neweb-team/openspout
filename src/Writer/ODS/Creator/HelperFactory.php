<?php

namespace NWT\OpenSpout\Writer\ODS\Creator;

use NWT\OpenSpout\Common\Helper\Escaper;
use NWT\OpenSpout\Common\Helper\StringHelper;
use NWT\OpenSpout\Common\Manager\OptionsManagerInterface;
use NWT\OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use NWT\OpenSpout\Writer\Common\Entity\Options;
use NWT\OpenSpout\Writer\Common\Helper\ZipHelper;
use NWT\OpenSpout\Writer\ODS\Helper\FileSystemHelper;

/**
 * Factory for helpers needed by the ODS Writer.
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

        return new FileSystemHelper($tempFolder, $zipHelper);
    }

    /**
     * @return Escaper\ODS
     */
    public function createStringsEscaper()
    {
        return new Escaper\ODS();
    }

    /**
     * @return StringHelper
     */
    public function createStringHelper()
    {
        return new StringHelper();
    }

    /**
     * @param InternalEntityFactory $entityFactory
     *
     * @return ZipHelper
     */
    private function createZipHelper($entityFactory)
    {
        return new ZipHelper($entityFactory);
    }
}
