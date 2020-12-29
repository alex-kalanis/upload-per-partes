<?php

namespace Support;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoStorage\AStorage;


/**
 * Class InfoRam
 * @package Support
 * Processing info file on ram volume
 */
class InfoRam extends AStorage
{
    protected $data = '';

    public function exists(string $key): bool
    {
        return !empty($this->data);
    }

    public function load(string $key): string
    {
        $content = $this->data;
        if (empty($content)) {
            throw new UploadException($this->lang->driveFileCannotRead());
        }
        return $content;
    }

    public function save(string $key, string $data): void
    {
        $this->data = $data;
    }

    public function remove(string $key): void
    {
        $this->data = '';
    }
}