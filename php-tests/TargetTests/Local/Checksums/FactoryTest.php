<?php

namespace TargetTests\Local\Checksums;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces\IChecksum;
use kalanis\UploadPerPartes\Target\Local\Checksums;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new Checksums\Factory();
        $this->assertInstanceOf(Checksums\Md5::class, $factory->getChecksum(Checksums\Factory::FORMAT_MD5));
        $this->assertInstanceOf(Checksums\Sha1::class, $factory->getChecksum(Checksums\Factory::FORMAT_SHA1));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(Checksums\Md5::class, $factory->getChecksum(Checksums\Md5::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new Checksums\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The checksum is set in a wrong way. Cannot determine it. *stdClass*');
        $factory->getChecksum(\stdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The checksum is set in a wrong way. Cannot determine it. *TargetTests\Local\Checksums\AXstdClass*');
        $factory->getChecksum(AXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getChecksum(999);
    }
}


class XFactory extends Checksums\Factory
{
    protected array $map = [
        '10' => \stdClass::class,
        '999' => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass implements IChecksum
{
}
