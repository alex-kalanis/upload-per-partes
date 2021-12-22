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
            throw new UploadException($this->lang->driveFileCannotRead());
        }
        return $content;
    }

    public function save(string $key, string $data): void
    {
        $this->data[$key] = $data;
    }

    public function remove(string $key): void
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }
    }
}
