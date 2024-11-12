<?php

namespace NWT\OpenSpout\Reader\XLSX;

use NWT\OpenSpout\Common\Exception\IOException;
use NWT\OpenSpout\Common\Helper\GlobalFunctionsHelper;
use NWT\OpenSpout\Common\Manager\OptionsManagerInterface;
use NWT\OpenSpout\Reader\Common\Creator\InternalEntityFactoryInterface;
use NWT\OpenSpout\Reader\Common\Entity\Options;
use NWT\OpenSpout\Reader\ReaderAbstract;
use NWT\OpenSpout\Reader\XLSX\Creator\InternalEntityFactory;
use NWT\OpenSpout\Reader\XLSX\Creator\ManagerFactory;

/**
 * This class provides support to read data from a XLSX file.
 */
class Reader extends ReaderAbstract
{
    /** @var ManagerFactory */
    protected $managerFactory;

    /** @var \ZipArchive */
    protected $zip;

    /** @var \NWT\OpenSpout\Reader\XLSX\Manager\SharedStringsManager Manages shared strings */
    protected $sharedStringsManager;

    /** @var SheetIterator To iterator over the XLSX sheets */
    protected $sheetIterator;

    public function __construct(
        OptionsManagerInterface $optionsManager,
        GlobalFunctionsHelper $globalFunctionsHelper,
        InternalEntityFactoryInterface $entityFactory,
        ManagerFactory $managerFactory
    ) {
        parent::__construct($optionsManager, $globalFunctionsHelper, $entityFactory);
        $this->managerFactory = $managerFactory;
    }

    /**
     * @param string $tempFolder Temporary folder where the temporary files will be created
     *
     * @return Reader
     */
    public function setTempFolder($tempFolder)
    {
        $this->optionsManager->setOption(Options::TEMP_FOLDER, $tempFolder);

        return $this;
    }

    /**
     * Returns whether stream wrappers are supported.
     *
     * @return bool
     */
    protected function doesSupportStreamWrapper()
    {
        return false;
    }

    /**
     * Opens the file at the given file path to make it ready to be read.
     * It also parses the sharedStrings.xml file to get all the shared strings available in memory
     * and fetches all the available sheets.
     *
     * @param string $filePath Path of the file to be read
     *
     * @throws \NWT\OpenSpout\Common\Exception\IOException            If the file at the given path or its content cannot be read
     * @throws \NWT\OpenSpout\Reader\Exception\NoSheetsFoundException If there are no sheets in the file
     */
    protected function openReader($filePath)
    {
        /** @var InternalEntityFactory $entityFactory */
        $entityFactory = $this->entityFactory;

        $this->zip = $entityFactory->createZipArchive();

        if (true === $this->zip->open($filePath)) {
            $tempFolder = $this->optionsManager->getOption(Options::TEMP_FOLDER);
            $this->sharedStringsManager = $this->managerFactory->createSharedStringsManager($filePath, $tempFolder, $entityFactory);

            if ($this->sharedStringsManager->hasSharedStrings()) {
                // Extracts all the strings from the sheets for easy access in the future
                $this->sharedStringsManager->extractSharedStrings();
            }

            $this->sheetIterator = $entityFactory->createSheetIterator(
                $filePath,
                $this->optionsManager,
                $this->sharedStringsManager
            );
        } else {
            throw new IOException("Could not open {$filePath} for reading.");
        }
    }

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @return SheetIterator To iterate over sheets
     */
    protected function getConcreteSheetIterator()
    {
        return $this->sheetIterator;
    }

    /**
     * Closes the reader. To be used after reading the file.
     */
    protected function closeReader()
    {
        if (null !== $this->zip) {
            $this->zip->close();
        }

        if (null !== $this->sharedStringsManager) {
            $this->sharedStringsManager->cleanup();
        }
    }
}
