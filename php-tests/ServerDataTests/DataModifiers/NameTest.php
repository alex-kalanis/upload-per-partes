<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class NameTest extends CommonTestClass
{
    public function testThru(): void
    {
        $lib = new DataModifiers\Name();
        $this->assertEquals('fghjkl.partial', $lib->getLimitedData($this->mockData()));
    }
}
