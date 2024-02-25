<?php

namespace DataStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\DataStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


class FormatsTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new DataStorage\Factory(new Translations());
        $this->assertInstanceOf(DataStorage\VolumeBasic::class, $factory->getFormat(DataStorage\Factory::FORMAT_VOLUME));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory(new Translations());
        $this->assertInstanceOf(DataStorage\VolumeAdv::class, $factory->getFormat(DataStorage\VolumeAdv::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassInstance(): void
    {
        $factory = new XFactory(new Translations());
        $this->assertInstanceOf(DataStorage\VolumeObject::class, $factory->getFormat(new DataStorage\VolumeObject()));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new DataStorage\Factory(new Translations());
        $this->expectExceptionMessage('TEMPORARY STORAGE NOT SET');
        $this->expectException(UploadException::class);
        $factory->getFormat(0);
    }

    /**
     * @throws UploadException
     */
    public function testClassFail(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('TEMPORARY STORAGE IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(10);
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('Class this-class-does-not-exists does not exist');
        $this->expectException(UploadException::class);
        $factory->getFormat(999);
    }

    /**
     * @throws UploadException
     */
    public function testAbstractClassDie(): void
    {
        $factory = new XFactory();
        $this->expectExceptionMessage('TEMPORARY STORAGE IS WRONG');
        $this->expectException(UploadException::class);
        $factory->getFormat(XstdClass::class);
    }
}


class XFactory extends DataStorage\Factory
{
    protected $map = [
        10 => XPassClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class XstdClass
{}


class XPassClass
{
    public function __construct($param)
    {
        // just for param
    }
}
