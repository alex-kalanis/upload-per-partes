<?php

namespace UploadPerPartes\Keys;

use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\Uploader\Translations;

/**
 * Class AKey
 * @package UploadPerPartes\Keys
 * Connect shared key and local details
 */
abstract class AKey
{
    const FILE_DRIVER_SUFF = '.partial';
    const FILE_UPLOAD_SUFF = '.upload';
    const FILE_SUFF_SEP = '.';
    const FILE_VER_SEP = '_';

    protected $lang = null;
    protected $targetDir = '/';
    protected $remoteFileName = '';
    protected $localFileName = '';
    protected $sharedKey = '';
    protected $targetLocation = '';

    public function __construct(Translations $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param string $key
     * @return string
     * @throws UploadException
     */
    abstract public function fromShared(string $key): string;

    /**
     * @param string $targetDir
     * @return $this
     */
    public function setTargetDir(string $targetDir): self
    {
        $this->targetDir = $targetDir;
        return $this;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setRemoteFileName(string $fileName): self
    {
        $this->remoteFileName = $fileName;
        return $this;
    }

    /**
     * @return string
     * @throws UploadException
     */
    public function getRemoteFileName(): string
    {
        $this->checkRemoteName();
        return $this->remoteFileName;
    }

    /**
     * @return AKey
     * @throws UploadException
     */
    public function process(): self
    {
        $this->checkRemoteName();
        $this->checkTargetDir();
        $this->localFileName = $this->generateLocalFileName();
        $this->sharedKey = $this->generateSharedKey();
        $this->targetLocation = $this->generateTargetLocation();
        return $this;
    }

    /**
     * @throws UploadException
     */
    protected function checkRemoteName(): void
    {
        if (empty($this->remoteFileName)) {
            throw new UploadException($this->lang->sentNameIsEmpty());
        }
    }

    /**
     * @throws UploadException
     */
    protected function checkTargetDir(): void
    {
        if (empty($this->targetDir)) {
            throw new UploadException($this->lang->targetDirIsEmpty());
        }
    }


    protected function generateLocalFileName(): string
    {
        return $this->findName($this->remoteFileName);
    }

    protected function generateSharedKey(): string
    {
        return $this->fileBase($this->localFileName) . static::FILE_DRIVER_SUFF;
    }

    protected function generateTargetLocation(): string
    {
        return $this->targetDir . $this->fileBase($this->localFileName) . static::FILE_UPLOAD_SUFF;
    }


    public function getFileName(): string
    {
        return $this->localFileName;
    }

    public function getNewSharedKey(): string
    {
        return $this->sharedKey;
    }

    public function getTargetLocation(): string
    {
        return $this->targetLocation;
    }


    /**
     * Find non-existing name
     * @param string $name
     * @return string
     */
    protected function findName(string $name): string
    {
        $name = $this->canonize($name);
        $suffix = $this->fileSuffix($name);
        $fileBase = $this->fileBase($name);
        if (is_file($this->targetDir . $name) && !is_file($this->targetDir . $name . static::FILE_DRIVER_SUFF)) {
            $i = 0;
            do {
                $location = $fileBase . static::FILE_VER_SEP . $i . static::FILE_SUFF_SEP . $suffix;
                $i++;
            } while ( is_file($this->targetDir . $location) );
            return $location;
        } else {
            return $name;
        }
    }

    protected function canonize(string $fileName): string
    {
        $f = preg_replace('/((&[[:alpha:]]{1,6};)|(&#[[:alnum:]]{1,7};))/', '', $fileName);
        $f = preg_replace('#[^[:alnum:]_\s\-\.]#', '', $f); // remove non-alnum + dots
        $f = preg_replace('#[\s]#', '_', $f); // whitespaces to underscore
        $fileSuffix = $this->fileSuffix($f);
        $fileBase = $this->fileBase($f);
        $nameLength = mb_strlen($fileSuffix);
        if (!$nameLength) {
            return mb_substr($fileBase, 0, 127); // win...
        }
        $c = mb_substr($fileBase, 0, (127 - $nameLength));
        return $c . static::FILE_SUFF_SEP . $fileSuffix;
    }

    protected function fileSuffix(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_SUFF_SEP);
        return ((false !== $pos) ? (
            (0 < $pos) ? mb_substr($fileName, $pos + 1) : ''
        ) : '');
    }

    protected function fileBase(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_SUFF_SEP);
        return ((false !== $pos) ? (
            (0 < $pos) ? mb_substr($fileName, 0, $pos) : mb_substr($fileName, 1)
        ) : $fileName);
    }
}