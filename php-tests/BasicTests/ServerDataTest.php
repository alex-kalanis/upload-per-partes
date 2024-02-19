<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\ServerData;


class ServerDataTest extends CommonTestClass
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testProcessorFailDecode(): void
    {
        $lib = new ServerData\Processor();
        $this->expectException(Exceptions\UploadException::class);
        $lib->readPack('řŘřŘ');
        $this->expectExceptionMessageMatches('CANNOT DECODE INCOMING DATA');
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testProcessorFailRebuild(): void
    {
        $lib = new ServerData\Processor();
        $this->expectException(Exceptions\UploadException::class);
        $lib->readPack('QEBALS1ub3QtYS1qc29u'); // @@@--not-a-json
        $this->expectExceptionMessageMatches('CANNOT DECODE INCOMING DATA');
    }
}
