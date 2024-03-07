<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class FullPathTest extends CommonTestClass
{
    public function testThru(): void
    {
        $lib = new DataModifiers\FullPath();
        $this->assertEquals($this->getTestDir() . 'abcdefabcdef', $lib->getLimitedData($this->mockData()));
    }
}
