<?php

namespace kalanis\UploadPerPartes\ServerData;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;


/**
 * Class TargetFileName
 * @package kalanis\UploadPerPartes\ServerData
 * Search possible file name
 */
class TargetFileName
{
    use TLang;

    public const FILE_DRIVER_SUFF = '.partial';
    public const FILE_UPLOAD_SUFF = '.upload';
    protected const FILE_EXT_SEP = '.';
    protected const FILE_VER_SEP = '.';
    protected const WIN_NAME_LEN_LIMIT = 110; // minus dot, len upload and part for multiple file upload - win allows max 128 chars, rest is for path

    /** @var Interfaces\IDataStorage */
    protected $dataStorage = null;
    /** @var bool */
    protected $sanitizeAlnum = true;
    /** @var bool */
    protected $sanitizeWhitespace = true;


    public function __construct(
        Interfaces\IDataStorage $dataStorage,
        ?Interfaces\IUPPTranslations $lang = null,
        bool $sanitizeWhitespace = true,
        bool $sanitizeAlnum = true
    )
    {
        $this->setUppLang($lang);
        $this->dataStorage = $dataStorage;
        $this->sanitizeAlnum = $sanitizeAlnum;
        $this->sanitizeWhitespace = $sanitizeWhitespace;
    }

    /**
     * @param string $targetDir
     * @param string $fileName
     * @throws UploadException
     * @return string
     */
    public function process(string $targetDir, string $fileName): string
    {
        $this->checkRemoteName($fileName);
        $this->checkTargetDir($targetDir);
        list($base, $ext) = $this->canonize($fileName);
        return $this->findFreeName($targetDir, $base, $ext);
    }

    /**
     * @param string $fileName
     * @throws UploadException
     */
    protected function checkRemoteName(string $fileName): void
    {
        if (empty($fileName)) {
            throw new UploadException($this->getUppLang()->uppSentNameIsEmpty());
        }
    }

    /**
     * @param string $targetDir
     * @throws UploadException
     */
    protected function checkTargetDir(string $targetDir): void
    {
        if (empty($targetDir)) {
            throw new UploadException($this->getUppLang()->uppTargetDirIsEmpty());
        }
    }

    /**
     * Find non-existing name
     * @param string $targetDir
     * @param string $fileBase
     * @param string $fileExt
     * @throws UploadException
     * @return string
     */
    protected function findFreeName(string $targetDir, string $fileBase, string $fileExt): string
    {
        $ext = $this->addExt($fileExt);
        if ($this->dataStorage->exists($targetDir . $fileBase . $ext)) {
            $i = 0;
            do {
                $location = $fileBase . static::FILE_VER_SEP . $i;
                $i++;
            } while ( $this->dataStorage->exists($targetDir . $location . $ext) );
            return $location . $ext;
        }
        return $fileBase . $ext;
    }

    /**
     * @param string $fileName
     * @return string[]
     */
    protected function canonize(string $fileName): array
    {
        $f = strval(preg_replace('/((&[[:alpha:]]{1,6};)|(&#[[:alnum:]]{1,7};))/', '', $fileName));
        if ($this->sanitizeAlnum) {
            $f = strval(preg_replace('#[^[:alnum:]_\s\-\.]#', '', $f)); // remove all which is not alnum or dots
        }
        if ($this->sanitizeWhitespace) {
            $f = strval(preg_replace('#[\s]#', '_', $f)); // whitespaces to underscore
        }
        $fileExt = $this->fileExt($f);
        $fileBase = mb_substr(
            $this->fileBase($f),
            0,
            (static::WIN_NAME_LEN_LIMIT - intval(mb_strlen($fileExt)))
        ); // win limit... cut more due possibility of uploading multiple files with same name
        return [$fileBase, $fileExt];
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

    protected function addExt(string $fileExt): string
    {
        return ($this->hasExt($fileExt) ? static::FILE_EXT_SEP . $fileExt : '' );
    }

    protected function hasExt(string $fileExt): bool
    {
        return (0 < mb_strlen($fileExt));
    }
}
