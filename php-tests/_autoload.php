<?php

function autoload($className)
{
    $className = preg_replace('/^UploadPerPartes/', '', $className);
    $className = str_replace('\\', '/', $className);
    $className = str_replace('_', '/', $className);

    if (is_file(__DIR__ . '/../php-src/' . $className . '.php')) {
        require_once(__DIR__ . '/../php-src/' . $className . '.php');
    }
}

spl_autoload_register('autoload');