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
    /** @var string[] */
    protected $data = [];

    public function addPart(string $location, string $content, ?int $seek = null): void
    {
        if (!$this->exists($location)) {
            $this->data[$location] = $content;
        } elseif (is_null($seek)) {
            $this->data[$location] .= $content;
        } elseif ($seek >= strlen($this->data[$location])) {
            $this->data[$location] .= $content;
        } else {
            $this->data[$location] = substr($this->data[$location], 0, $seek) . $content;
        }
    }

    public function getPart(string $location, int $offset, ?int $limit = null): string
    {
        if (!$this->exists($location)) {
            return '';
        }
        return Strings::substr($this->data[$location], $offset, $limit, $this->getUppLang()->uppCannotReadFile($location));
    }

    public function truncate(string $location, int $offset): void
    {
        if ($this->exists($location) && (strlen($this->data[$location]) > $offset)) {
            $this->data[$location] = Strings::substr($this->data[$location], 0, $offset, $this->getUppLang()->uppCannotTruncateFile($location));
        }
    }

    public function remove(string $location): void
    {
        if ($this->exists($location)) {
            unset($this->data[$location]);
        }
    }

    public function exists(string $location): bool
    {
        return isset($this->data[$location]);
    }

    /**
     * @param string $location
     * @throws UploadException
     * @return string
     */
    public function getAll(string $location = ''): string
    {
        return $this->getPart($location, 0, null);
    }
}
