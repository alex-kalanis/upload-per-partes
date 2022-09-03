<?php

namespace kalanis\UploadPerPartes\Interfaces;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat\Data;


/**
 * Interface IInfoFormatting
 * @package kalanis\UploadPerPartes\Interfaces
 * Drive file format - abstract for each variant
 */
interface IInfoFormatting
{
    /**
     * @param string $content
     * @throws UploadException
     * @return Data
     */
    public function fromFormat(string $content): Data;

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function toFormat(Data $data): string;
}
