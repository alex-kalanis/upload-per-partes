<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\ServerData\Data;


/**
 * Interface ILimitPassedData
 * @package kalanis\UploadPerPartes\Interfaces
 * How to modify data for usage as storage key
 */
interface ILimitPassedData
{
    public function getLimitedData(Data $data): string;
}
