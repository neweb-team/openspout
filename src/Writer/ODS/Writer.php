<?php

namespace NWT\OpenSpout\Writer\ODS;

use NWT\OpenSpout\Writer\Common\Entity\Options;
use NWT\OpenSpout\Writer\WriterMultiSheetsAbstract;

/**
 * This class provides base support to write data to ODS files.
 */
class Writer extends WriterMultiSheetsAbstract
{
    /** @var string Content-Type value for the header */
    protected static $headerContentType = 'application/vnd.oasis.opendocument.spreadsheet';

    /**
     * Sets a custom temporary folder for creating intermediate files/folders.
     * This must be set before opening the writer.
     *
     * @param string $tempFolder Temporary folder where the files to create the ODS will be stored
     *
     * @throws \NWT\OpenSpout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     *
     * @return Writer
     */
    public function setTempFolder($tempFolder)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->optionsManager->setOption(Options::TEMP_FOLDER, $tempFolder);

        return $this;
    }
}
