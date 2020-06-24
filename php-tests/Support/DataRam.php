<?php

namespace Support;

use UploadPerPartes\DataStorage\AStorage;

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

    public function getPart(string $location, int $offset, int $limit): string
    {
        return substr($this->data, $offset, $limit);
    }

    public function truncate(string $location, int $offset): void
    {
        $this->data = substr($this->data, 0, $offset);
    }

    public function remove(string $key): void
    {
        $this->data = '';
    }
}