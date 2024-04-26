<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage;


use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\Uploader\Data;
use kalanis\UploadPerPartes\UploadException;


/**
 * Class FinalStorage
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage
 * Actions over final storage
 */
class FinalStorage
{
    protected IFinalStorage $storage;
    protected KeyEncoders\AEncoder $keyEncoder;

    public function __construct(
        IFinalStorage $storage,
        KeyEncoders\AEncoder $keyEncoder
    )
    {
        $this->storage = $storage;
        $this->keyEncoder = $keyEncoder;
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return bool
     */
    public function exists(Data $data): bool
    {
        return $this->storage->exists($this->keyEncoder->toPath($data));
    }

    /**
     * @param string $key
     * @param resource $source
     * @throws UploadException
     * @return bool
     */
    public function store(string $key, $source): bool
    {
        return $this->storage->store($key, $source);
    }

    /**
     * @param Data $data
     * @throws UploadException
     * @return string
     */
    public function findName(Data $data): string
    {
        return $this->storage->findName($this->keyEncoder->toPath($data));
    }
}
