<?php

namespace Support;


use kalanis\UploadPerPartes\DataStorage\AStorage;
use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class DataRam
 * @package Support
 * Processing data file on ram volume
 */
class DataRam extends AStorage
{
    protected $data = '';

    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        $this->data = ( is_null($seek) ? $this->data : substr($this->data, 0, $seek) ) . $content;
    }

    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        return Strings::substr($this->data, $offset, $limit, $this->lang->cannotReadFile());
    }

    public function truncate(string $location, int $offset): void
    {
        $this->data = Strings::substr($this->data, 0, $offset, $this->lang->cannotTruncateFile());
    }

    public function remove(string $location): void
    {
        $this->data = '';
    }

    /**
     * @param string $location
     * @return string
     * @throws UploadException
     */
    public function getAll(string $location = ''): string
    {
        return $this->getPart($location, 0, null);
    }
}