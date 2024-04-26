<?php

namespace TargetTests\Local\DrivingFile\Storage;


use CommonTestClass;
use kalanis\kw_files\Access;
use kalanis\kw_storage\Storage as kw_store;
use kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit1(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Volume::class, $factory->getStorage(new Config(['driving_file' => 'volume'])));
    }

    /**
     * @throws UploadException
     */
    public function testInit2(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Client::class, $factory->getStorage(new Config(['driving_file' => 'client'])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Volume::class, $factory->getStorage(new Config(['driving_file' => '/tmp/'])));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Storage::class, $factory->getStorage(new Config(['driving_file' => new Storage\Storage(new kw_store\Storage(new kw_store\Key\DefaultKey(), new kw_store\Target\Memory()))])));
    }

    /**
     * @throws UploadException
     */
    public function testInitRemoteClassInstance1(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Storage::class, $factory->getStorage(new Config(['driving_file' => new kw_store\Storage(new kw_store\Key\DefaultKey(), new kw_store\Target\Memory())])));
    }

    /**
     * @throws UploadException
     * @throws \kalanis\kw_files\FilesException
     * @throws \kalanis\kw_paths\PathsException
     */
    public function testInitRemoteClassInstance2(): void
    {
        $factory = new Storage\Factory();
        $this->assertInstanceOf(Storage\Files::class, $factory->getStorage(new Config(['driving_file' => (new Access\Factory())->getClass(new kw_store\Storage(new kw_store\Key\DefaultKey(), new kw_store\Target\Memory()))])));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new Storage\Factory();
        $conf = new Config([]);
        $conf->finalStorage = new \stdClass();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving file storage is not set correctly!');
        $factory->getStorage($conf);
    }
}
