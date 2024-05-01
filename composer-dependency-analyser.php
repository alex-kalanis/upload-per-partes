<?php

/**
 * Dependency analyzer configuration
 * @link https://github.com/shipmonk-rnd/composer-dependency-analyser
 */

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    // ignore errors on specific packages and paths
    ->ignoreErrorsOnPackageAndPath('predis/predis', __DIR__ . '/php-src/Target/Local/DrivingFile/Storage/Predis.php', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->addPathToExclude(__DIR__ . '/php-src/Target/Remote/Psr')
;
