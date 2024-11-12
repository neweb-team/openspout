<?php

namespace NWT\OpenSpout\Reader\Common\Creator;

use NWT\OpenSpout\Common\Creator\HelperFactory;
use NWT\OpenSpout\Common\Exception\UnsupportedTypeException;
use NWT\OpenSpout\Common\Type;
use NWT\OpenSpout\Reader\CSV\Creator\InternalEntityFactory as CSVInternalEntityFactory;
use NWT\OpenSpout\Reader\CSV\Manager\OptionsManager as CSVOptionsManager;
use NWT\OpenSpout\Reader\CSV\Reader as CSVReader;
use NWT\OpenSpout\Reader\ODS\Creator\HelperFactory as ODSHelperFactory;
use NWT\OpenSpout\Reader\ODS\Creator\InternalEntityFactory as ODSInternalEntityFactory;
use NWT\OpenSpout\Reader\ODS\Creator\ManagerFactory as ODSManagerFactory;
use NWT\OpenSpout\Reader\ODS\Manager\OptionsManager as ODSOptionsManager;
use NWT\OpenSpout\Reader\ODS\Reader as ODSReader;
use NWT\OpenSpout\Reader\ReaderInterface;
use NWT\OpenSpout\Reader\XLSX\Creator\HelperFactory as XLSXHelperFactory;
use NWT\OpenSpout\Reader\XLSX\Creator\InternalEntityFactory as XLSXInternalEntityFactory;
use NWT\OpenSpout\Reader\XLSX\Creator\ManagerFactory as XLSXManagerFactory;
use NWT\OpenSpout\Reader\XLSX\Manager\OptionsManager as XLSXOptionsManager;
use NWT\OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactory;
use NWT\OpenSpout\Reader\XLSX\Reader as XLSXReader;

/**
 * This factory is used to create readers, based on the type of the file to be read.
 * It supports CSV, XLSX and ODS formats.
 */
class ReaderFactory
{
    /**
     * Creates a reader by file extension.
     *
     * @param string $path The path to the spreadsheet file. Supported extensions are .csv,.ods and .xlsx
     *
     * @throws \NWT\OpenSpout\Common\Exception\UnsupportedTypeException
     *
     * @return ReaderInterface
     */
    public static function createFromFile(string $path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return self::createFromType($extension);
    }

    /**
     * This creates an instance of the appropriate reader, given the type of the file to be read.
     *
     * @param string $readerType Type of the reader to instantiate
     *
     * @throws \NWT\OpenSpout\Common\Exception\UnsupportedTypeException
     *
     * @return ReaderInterface
     */
    public static function createFromType($readerType)
    {
        switch ($readerType) {
            case Type::CSV: return self::createCSVReader();

            case Type::XLSX: return self::createXLSXReader();

            case Type::ODS: return self::createODSReader();

            default:
                throw new UnsupportedTypeException('No readers supporting the given type: '.$readerType);
        }
    }

    /**
     * @return CSVReader
     */
    private static function createCSVReader()
    {
        $optionsManager = new CSVOptionsManager();
        $helperFactory = new HelperFactory();
        $entityFactory = new CSVInternalEntityFactory($helperFactory);
        $globalFunctionsHelper = $helperFactory->createGlobalFunctionsHelper();

        return new CSVReader($optionsManager, $globalFunctionsHelper, $entityFactory);
    }

    /**
     * @return XLSXReader
     */
    private static function createXLSXReader()
    {
        $optionsManager = new XLSXOptionsManager();
        $helperFactory = new XLSXHelperFactory();
        $managerFactory = new XLSXManagerFactory($helperFactory, new CachingStrategyFactory());
        $entityFactory = new XLSXInternalEntityFactory($managerFactory, $helperFactory);
        $globalFunctionsHelper = $helperFactory->createGlobalFunctionsHelper();

        return new XLSXReader($optionsManager, $globalFunctionsHelper, $entityFactory, $managerFactory);
    }

    /**
     * @return ODSReader
     */
    private static function createODSReader()
    {
        $optionsManager = new ODSOptionsManager();
        $helperFactory = new ODSHelperFactory();
        $managerFactory = new ODSManagerFactory();
        $entityFactory = new ODSInternalEntityFactory($helperFactory, $managerFactory);
        $globalFunctionsHelper = $helperFactory->createGlobalFunctionsHelper();

        return new ODSReader($optionsManager, $globalFunctionsHelper, $entityFactory);
    }
}
