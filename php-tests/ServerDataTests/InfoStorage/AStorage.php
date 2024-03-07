<?php

namespace ServerDataTests\InfoStorage;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\InfoStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


abstract class AStorage extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            $this->mockStorage()->remove($this->mockTestFile());
        }
        parent::tearDown();
    }

    protected function mockStorage(): InfoStorage\AStorage
    {
        return new InfoStorage\Volume(new Translations());
    }
}
