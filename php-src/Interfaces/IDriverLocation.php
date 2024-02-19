<?php

namespace kalanis\UploadPerPartes\Interfaces;


/**
 * Interface IDriverLocation
 * @package kalanis\UploadPerPartes\Interfaces
 * Drive file location
 */
interface IDriverLocation
{
    public function getDriverPrefix(): string;

    public function getDriverKey(): string;
}
