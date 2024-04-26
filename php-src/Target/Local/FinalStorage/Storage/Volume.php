<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage;


use kalanis\kw_paths\Stuff;
use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class Files
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\Storage
 * Where to store data on target destination - storage is local volume
 */
class Volume implements IFinalStorage
{
    use TLang;

    protected string $pathPrefix = '';

    public function __construct(string $pathPrefix = '', IUppTranslations $lang = null)
    {
        $this->pathPrefix = $pathPrefix;
        $this->setUppLang($lang);
    }

    public function exists(string $key): bool
    {
        return @file_exists($this->fullPath($key));
    }

    public function store(string $key, $data): bool
    {
        @rewind($data);
        return false !== @file_put_contents($this->fullPath($key), $data);
    }

    public function findName(string $key): string
    {
        if (!$this->exists($key)) {
            return $key;
        }

        $name = Stuff::fileBase($key);
        $suffix = Stuff::fileExt($key);

        $i = 0;
        while ($this->exists($name . $this->getNameSeparator() . strval($i) . '.' . $suffix)) {
            $i++;
        }
        return $name . $this->getNameSeparator() . strval($i) . '.' . $suffix;
    }

    protected function fullPath(string $key): string
    {
        return $this->pathPrefix . $key;
    }

    protected function getNameSeparator(): string
    {
        return '__';
    }
}
