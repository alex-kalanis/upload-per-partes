<?php

function autoload($className)
{
    if (!defined('PROJECT_NAME')) {
        define('PROJECT_NAME', '.');
    }
    if (!defined('PROJECT_DIR')) {
        define('PROJECT_DIR', 'src');
    }

    $className = preg_replace('/^' . PROJECT_NAME . '/', '', $className);
    $className = str_replace('\\', '/', $className);
    $className = str_replace('_', '/', $className);

    if (is_file(__DIR__ . '/' . $className . '.php')) {
        require_once(__DIR__ . '//' . $className . '.php');
    }

    if (is_file(__DIR__ . '/../' . PROJECT_DIR . '/' . $className . '.php')) {
        require_once(__DIR__ . '/../' . PROJECT_DIR . '/' . $className . '.php');
    }
}

spl_autoload_register('autoload');