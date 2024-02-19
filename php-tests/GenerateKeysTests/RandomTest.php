<?php

namespace GenerateKeysTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\Data;
use kalanis\UploadPerPartes\GenerateKeys;


class RandomTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $data = new Data();
        $data->sharedKey = 'lkjhg';
        $lib = new GenerateKeys\Random();
        $this->assertNotEmpty($lib->generateKey($data));
    }

    public function testRandom(): void
    {
        $this->assertEquals('aaaaaaa', GenerateKeys\Random::generateRandomText(7, ['a','a','a','a']));
    }
}
