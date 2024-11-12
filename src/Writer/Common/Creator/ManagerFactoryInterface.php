<?php

namespace NWT\OpenSpout\Writer\Common\Creator;

use NWT\OpenSpout\Common\Manager\OptionsManagerInterface;
use NWT\OpenSpout\Writer\Common\Manager\SheetManager;
use NWT\OpenSpout\Writer\Common\Manager\WorkbookManagerInterface;

/**
 * Interface ManagerFactoryInterface.
 */
interface ManagerFactoryInterface
{
    /**
     * @return WorkbookManagerInterface
     */
    public function createWorkbookManager(OptionsManagerInterface $optionsManager);

    /**
     * @return SheetManager
     */
    public function createSheetManager();
}
