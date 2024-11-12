<?php

namespace NWT\OpenSpout\Autoloader;

require_once 'Psr4Autoloader.php';

/**
 * @var string
 *             Full path to "src/" which is what we want "NWT\OpenSpout" to map to
 */
$srcBaseDirectory = \dirname(__DIR__);

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('NWT\OpenSpout', $srcBaseDirectory);
