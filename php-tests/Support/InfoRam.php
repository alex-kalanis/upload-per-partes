<?php

namespace Support;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\InfoStorage\AStorage;


/**
 * Class InfoRam
 * @package Support
 * Processing info file on ram volume
 */
class InfoRam extends AStorage
{
    /** @var string[] */
    protected $data = [];

    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function load(string $key): string
    {
        $content = $this->exists($key) ? $this->data[$key] : '';
        if (empty($content)) {
            throw new UploadException($this->getUppLang()->uppDriveFileCannotRead($key));
        }
        return $content;
    }

    public function save(string $key, string $data): bool
    {
        $this->data[$key] = $data;
        return true;
    }

    public function remove(string $key): bool
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }
        return true;
    }

    public function checkKeyClasses(object $limitData, object $storageKeys, object $infoFormat): bool
    {
       return true;
    }
}
