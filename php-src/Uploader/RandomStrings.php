<?php

namespace kalanis\UploadPerPartes\Uploader;


/**
 * Class RandomStrings
 * @package kalanis\UploadPerPartes\Uploader
 * Generating random strings
 */
class RandomStrings
{
    /** @var string[] */
    public static array $possibilities = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];

    public static function generate(int $length = 64): string
    {
        return static::generateRandomText($length, static::$possibilities);
    }

    /**
     * @param int $length
     * @param string[] $possibilities
     * @return string
     */
    public static function generateRandomText(int $length, array $possibilities): string
    {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, count($possibilities) - 1);
            $string .= $possibilities[$rand];
        }
        return $string;
    }

    public static function randomLength(): string
    {
        $which = md5(strval(rand()));
        return strval(substr($which, 0, intval(hexdec(bin2hex(substr($which, -1))))));
    }
}
