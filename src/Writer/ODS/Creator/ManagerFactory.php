<?php

namespace NWT\OpenSpout\Writer\ODS\Creator;

use NWT\OpenSpout\Common\Manager\OptionsManagerInterface;
use NWT\OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use NWT\OpenSpout\Writer\Common\Creator\ManagerFactoryInterface;
use NWT\OpenSpout\Writer\Common\Entity\Options;
use NWT\OpenSpout\Writer\Common\Manager\SheetManager;
use NWT\OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use NWT\OpenSpout\Writer\ODS\Manager\Style\StyleManager;
use NWT\OpenSpout\Writer\ODS\Manager\Style\StyleRegistry;
use NWT\OpenSpout\Writer\ODS\Manager\WorkbookManager;
use NWT\OpenSpout\Writer\ODS\Manager\WorksheetManager;

/**
 * Factory for managers needed by the ODS Writer.
 */
class ManagerFactory implements ManagerFactoryInterface
{
    /** @var InternalEntityFactory */
    protected $entityFactory;

    /** @var HelperFactory */
    protected $helperFactory;

    public function __construct(InternalEntityFactory $entityFactory, HelperFactory $helperFactory)
    {
        $this->entityFactory = $entityFactory;
        $this->helperFactory = $helperFactory;
    }

    /**
     * @return WorkbookManager
     */
    public function createWorkbookManager(OptionsManagerInterface $optionsManager)
    {
        $workbook = $this->entityFactory->createWorkbook();

        $fileSystemHelper = $this->helperFactory->createSpecificFileSystemHelper($optionsManager, $this->entityFactory);
        $fileSystemHelper->createBaseFilesAndFolders();

        $styleMerger = $this->createStyleMerger();
        $styleManager = $this->createStyleManager($optionsManager);
        $worksheetManager = $this->createWorksheetManager($styleManager, $styleMerger);

        return new WorkbookManager(
            $workbook,
            $optionsManager,
            $worksheetManager,
            $styleManager,
            $styleMerger,
            $fileSystemHelper,
            $this->entityFactory,
            $this
        );
    }

    /**
     * @return SheetManager
     */
    public function createSheetManager()
    {
        $stringHelper = $this->helperFactory->createStringHelper();

        return new SheetManager($stringHelper);
    }

    /**
     * @return WorksheetManager
     */
    private function createWorksheetManager(StyleManager $styleManager, StyleMerger $styleMerger)
    {
        $stringsEscaper = $this->helperFactory->createStringsEscaper();
        $stringsHelper = $this->helperFactory->createStringHelper();

        return new WorksheetManager($styleManager, $styleMerger, $stringsEscaper, $stringsHelper);
    }

    /**
     * @return StyleManager
     */
    private function createStyleManager(OptionsManagerInterface $optionsManager)
    {
        $styleRegistry = $this->createStyleRegistry($optionsManager);

        return new StyleManager($styleRegistry, $optionsManager);
    }

    /**
     * @return StyleRegistry
     */
    private function createStyleRegistry(OptionsManagerInterface $optionsManager)
    {
        $defaultRowStyle = $optionsManager->getOption(Options::DEFAULT_ROW_STYLE);

        return new StyleRegistry($defaultRowStyle);
    }

    /**
     * @return StyleMerger
     */
    private function createStyleMerger()
    {
        return new StyleMerger();
    }
}
