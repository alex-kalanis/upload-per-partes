<?php

namespace TraitsTests;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\InfoFormat\Data;
use kalanis\UploadPerPartes\Traits\TData;


class DataTest extends \CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testSimple(): void
    {
        $lib = new XData();
        $lib->setInfoData(new Data());
        $this->assertInstanceOf(Data::class, $lib->getInfoData());
    }

    /**
     * @throws UploadException
     */
    public function testClassDie(): void
    {
        $lib = new XData();
        $this->expectException(UploadException::class);
        $lib->getInfoData();
    }
}


class XData
{
    use TData;
}
