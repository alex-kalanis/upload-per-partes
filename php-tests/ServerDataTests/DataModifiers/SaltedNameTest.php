<?php

namespace ServerDataTests\DataModifiers;


use CommonTestClass;
use kalanis\UploadPerPartes\ServerData\DataModifiers;


class SaltedNameTest extends CommonTestClass
{
    public function testThru(): void
    {
        $lib = new DataModifiers\SaltedName();
        // salted, cannot check exact
        $this->assertNotEmpty($lib->getLimitedData($this->mockData()));
    }
}
