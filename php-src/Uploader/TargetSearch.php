<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Exceptions\UploadException;


/**
 * Class TargetSearch
 * @package kalanis\UploadPerPartes\Uploader
 * Search possible target path
 */
class TargetSearch
{
    const FILE_DRIVER_SUFF = '.partial';
    const FILE_UPLOAD_SUFF = '.upload';
    const FILE_EXT_SEP = '.';
    const FILE_VER_SEP = '.';
    const WIN_NAME_LEN_LIMIT = 110; // minus dot, len upload and part for multiple file upload - win allows max 128 chars, rest is for path

    /** @var Translations|null */
    protected $lang = null;
    /** @var bool */
    protected $sanitizeAlnum = true;
    /** @var bool */
    protected $sanitizeWhitespace = true;

    /** @var string */
    protected $remoteFileName = '';
    /** @var string  */
    protected $targetDir = '';

    /** @var string */
    protected $fileBase = '';
    /** @var string  */
    protected $fileExt = '';


    /**
     * @param Translations $lang
     * @param bool $sanitizeWhitespace
     * @param bool $sanitizeAlnum
     */
    public function __construct(Translations $lang, bool $sanitizeWhitespace = true, bool $sanitizeAlnum = true)
    {
        $this->lang = $lang;
        $this->sanitizeAlnum = $sanitizeAlnum;
        $this->sanitizeWhitespace = $sanitizeWhitespace;
    }

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
     * @return $this
     * @throws UploadException
     */
    public function process(): self
    {
        $this->checkRemoteName();
        $this->checkTargetDir();
        $this->canonize();
        $this->findFreeName();
        return $this;
    }


    /**
     * @return string
     * @throws UploadException
     */
    public function getDriverLocation(): string
    {
        $this->checkFileBase();
        return $this->targetDir . $this->fileBase . static::FILE_DRIVER_SUFF;
    }

    /**
     * @return string
     * @throws UploadException
     */
    public function getFinalTargetName(): string
    {
        $this->checkFileBase();
        return $this->fileBase . $this->addExt();
    }

    /**
     * @return string
     * @throws UploadException
     */
    public function getTemporaryTargetLocation(): string
    {
        $this->checkFileBase();
        return $this->targetDir . $this->fileBase . $this->addExt(). static::FILE_UPLOAD_SUFF;
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

    /**
     * @throws UploadException
     */
    protected function checkFileBase(): void
    {
        if (empty($this->fileBase)) {
            throw new UploadException($this->lang->uploadNameIsEmpty());
        }
    }


    /**
     * Find non-existing name
     * @throws UploadException
     */
    protected function findFreeName(): void
    {
        $ext = $this->addExt();
        if (    is_file($this->targetDir . $this->fileBase . $ext)
            && !is_file($this->getDriverLocation())
        ) {
            $i = 0;
            do {
                $location = $this->fileBase . static::FILE_VER_SEP . $i;
                $i++;
            } while ( is_file($this->targetDir . $location . $ext) );
            $this->fileBase = $location;
        }
    }

    protected function canonize(): void
    {
        $f = preg_replace('/((&[[:alpha:]]{1,6};)|(&#[[:alnum:]]{1,7};))/', '', $this->remoteFileName);
        if ($this->sanitizeAlnum) {
            $f = preg_replace('#[^[:alnum:]_\s\-\.]#', '', $f); // remove all which is not alnum or dots
        }
        if ($this->sanitizeWhitespace) {
            $f = preg_replace('#[\s]#', '_', $f); // whitespaces to underscore
        }
        $this->fileExt = $this->fileExt($f);
        $this->fileBase = mb_substr(
            $this->fileBase($f),
            0,
            (static::WIN_NAME_LEN_LIMIT - (int)mb_strlen($this->fileExt))
        ); // win limit... cut more due possibility of uploading multiple files with same name
    }

    protected function fileExt(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_EXT_SEP);
        return ((false !== $pos) ? (
            (0 < $pos) ? mb_substr($fileName, $pos + 1) : ''
        ) : '');
    }

    protected function fileBase(string $fileName): string
    {
        $pos = mb_strrpos($fileName, static::FILE_EXT_SEP);
        return ((false !== $pos) ? (
            (0 < $pos) ? mb_substr($fileName, 0, $pos) : mb_substr($fileName, 1)
        ) : $fileName);
    }

    protected function addExt(): string
    {
        return ($this->hasExt() ? static::FILE_EXT_SEP . $this->fileExt : '' );
    }

    protected function hasExt(): bool
    {
        return (0 < mb_strlen($this->fileExt));
    }
}
