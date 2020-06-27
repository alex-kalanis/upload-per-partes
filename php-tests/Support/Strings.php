<?php

namespace Support;

use UploadPerPartes\Exceptions\UploadException;

/**
 * Class Strings
 * @package UploadPerPartes\DriveFile
 * Processing Strings - reimplementation of necessary methods (contains fuckups)
 */
class Strings
{
    /**
     * Original one returns shitty results, so need re-implement parts
     * @param string $what
     * @param int $offset
     * @param int|null $limit
     * @param string $errorMessage
     * @return string
     * @throws UploadException
     */
    public static function substr(string $what, int $offset, ?int $limit, string $errorMessage = ''): string
    {
        $length = strlen($what);
        if (!is_null($limit) && ($limit > $length)) { // not over
            $limit = null;
        }
        if (empty($limit)) {
            $result = (!empty($offset)) ? substr($what, $offset) : $what ;
        } else {
            $result = (!empty($offset)) ? substr($what, $offset, $limit) : $what[0] . substr($what, 1, $limit - 1); // THIS ugly hack
        }
        if (false === $result) {
            throw new UploadException($errorMessage); // failed substr
        }
        return $result;
    }
}