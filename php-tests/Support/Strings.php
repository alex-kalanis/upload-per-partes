<?php

namespace Support;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class Strings
 * @package Support
 * Processing Strings - reimplementation of necessary methods (contains fuckups)
 */
class Strings
{
    /**
     * Original one returns shitty results, so need re-implement parts
     * It's necessary to have nullable limit, not only set as undefined
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
            $result = (!empty($offset)) ? substr($what, $offset, $limit) : substr($what, 0, $limit);
        }
        if (false === $result) {
            throw new UploadException($errorMessage); // failed substr
        }
        return $result;
    }
}