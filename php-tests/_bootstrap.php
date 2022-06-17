<?php

define('AUTHOR_NAME', 'kalanis');
define('PROJECT_NAME', 'UploadPerPartes');
define('PROJECT_DIR', 'php-src');

$composter = realpath(__DIR__ . '/../vendor/autoload.php');
if ($composter) {
    $loader = @require_once $composter;
//    $loader->addPsr4(implode('\\', [AUTHOR_NAME, PROJECT_NAME]), __DIR__);
}

require_once __DIR__ . '/_autoload.php';
require_once __DIR__ . '/CommonTestClass.php';
