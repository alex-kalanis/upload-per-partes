<?php

namespace kalanis\UploadPerPartes\Target\Local\TemporaryStorage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class TemporaryStorage
 * @package kalanis\UploadPerPartes\Target\Local\TemporaryStorage
 * Actions over temporary storage
 */
class TemporaryStorage
{
    use TLang;

    protected Interfaces\ITemporaryStorage $storage;
    protected KeyEncoders\AEncoder $keyModifier;

    public function __construct(
        Interfaces\ITemporaryStorage $storage,
        KeyEncoders\AEncoder $keyModifier,
        ?Interfaces\IUppTranslations $lang = null
    )
    {
        $this->storage = $storage;
        $this->keyModifier = $keyModifier;
        $this->setUppLang($lang);
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return bool
     */
    public function exists(Data $data): bool
    {
        return $this->storage->exists($this->keyModifier->toPath($data));
    }

    /**
     * @param Data $data
     * @param int<0, max> $fromByte
     * @throws UploadException
     * @return string
     */
    public function checksumData(Data $data, int $fromByte): string
    {
        $data = $this->storage->readData($this->keyModifier->toPath($data), $fromByte, $data->bytesPerPart);
        if (empty($data)) {
            throw new UploadException($this->getUppLang()->uppChecksumIsEmpty());
        }
        return $data;
    }

    /**
     * @param Data $data
     * @param int<0, max> $fromByte
     * @throws UploadException
     * @return bool
     */
    public function truncate(Data $data, int $fromByte): bool
    {
        return $this->storage->truncate($this->keyModifier->toPath($data), $fromByte);
    }

    /**
     * @param Data $data
     * @param string $content decoded content from client
     * @throws UploadException
     * @return bool
     */
    public function upload(Data $data, string $content): bool
    {
        return $this->storage->append($this->keyModifier->toPath($data), $content);
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return resource stream
     */
    public function read(Data $data)
    {
        return $this->storage->readStream($this->keyModifier->toPath($data));
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return bool
     */
    public function remove(Data $data)
    {
        return $this->storage->remove($this->keyModifier->toPath($data));
    }
}
