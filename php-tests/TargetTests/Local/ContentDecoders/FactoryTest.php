<?php

namespace TargetTests\Local\ContentDecoders;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces\IContentDecoder;
use kalanis\UploadPerPartes\Target\Local\ContentDecoders;
use kalanis\UploadPerPartes\Traits\TLangInit;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $factory = new ContentDecoders\Factory();
        $this->assertInstanceOf(ContentDecoders\Base64::class, $factory->getDecoder(ContentDecoders\Factory::FORMAT_BASE64));
        $this->assertInstanceOf(ContentDecoders\Hex::class, $factory->getDecoder(ContentDecoders\Factory::FORMAT_HEX));
    }

    /**
     * @throws UploadException
     */
    public function testInitOwnClassString(): void
    {
        $factory = new XFactory();
        $this->assertInstanceOf(ContentDecoders\Base64::class, $factory->getDecoder(ContentDecoders\Base64::class));
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $factory = new ContentDecoders\Factory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The decoder is set in a wrong way. Cannot determine it. *TargetTests\Local\ContentDecoders\XstdClass*');
        $factory->getDecoder(XstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassAbstractFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The decoder is set in a wrong way. Cannot determine it. *TargetTests\Local\ContentDecoders\AXstdClass*');
        $factory->getDecoder(AXstdClass::class);
    }

    /**
     * @throws UploadException
     */
    public function testClassNotExistsFail(): void
    {
        $factory = new XFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "this-class-does-not-exists" does not exist');
        $factory->getDecoder(999);
    }
}


class XFactory extends ContentDecoders\Factory
{
    protected array $map = [
        10 => \stdClass::class,
        999 => 'this-class-does-not-exists',
    ];
}


abstract class AXstdClass implements IContentDecoder
{
    use TLangInit;
}


class XstdClass
{
    use TLangInit;
}
