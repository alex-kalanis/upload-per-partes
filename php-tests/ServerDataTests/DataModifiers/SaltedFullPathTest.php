<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class SaltedFullPathTest extends CommonTestClass
{
    public function testThru(): void
    {
        $lib = new DataModifiers\SaltedFullPath();
        // salted, cannot check exact
        $this->assertNotEmpty($lib->getLimitedData($this->mockData()));
    }
}
