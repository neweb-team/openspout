<?php

namespace NWT\OpenSpout\Writer\Exception\Border;

use NWT\OpenSpout\Common\Entity\Style\BorderPart;
use NWT\OpenSpout\Writer\Exception\WriterException;

class InvalidStyleException extends WriterException
{
    public function __construct($name)
    {
        $msg = '%s is not a valid style identifier for a border. Valid identifiers are: %s.';

        parent::__construct(sprintf($msg, $name, implode(',', BorderPart::getAllowedStyles())));
    }
}
