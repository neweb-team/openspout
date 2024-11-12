<?php

namespace NWT\OpenSpout\Reader\ODS\Creator;

use NWT\OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use NWT\OpenSpout\Reader\ODS\Helper\SettingsHelper;

/**
 * Factory to create helpers.
 */
class HelperFactory extends \NWT\OpenSpout\Common\Creator\HelperFactory
{
    /**
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     *
     * @return CellValueFormatter
     */
    public function createCellValueFormatter($shouldFormatDates)
    {
        $escaper = $this->createStringsEscaper();

        return new CellValueFormatter($shouldFormatDates, $escaper);
    }

    /**
     * @param InternalEntityFactory $entityFactory
     *
     * @return SettingsHelper
     */
    public function createSettingsHelper($entityFactory)
    {
        return new SettingsHelper($entityFactory);
    }

    /**
     * @return \NWT\OpenSpout\Common\Helper\Escaper\ODS
     */
    public function createStringsEscaper()
    {
        // @noinspection PhpUnnecessaryFullyQualifiedNameInspection
        return new \NWT\OpenSpout\Common\Helper\Escaper\ODS();
    }
}
