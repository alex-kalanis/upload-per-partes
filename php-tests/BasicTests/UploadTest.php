<?php

namespace BasicTests;

use CommonTestClass;
use UploadPerPartes\Keys;
use UploadPerPartes\Response;
use UploadPerPartes\Storage;
use UploadPerPartes\Uploader;
use UploadPerPartes\Uploader\Translations;

class UploadTest extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new Storage\Volume(Translations::init());
            $lib->remove($this->mockTestFile());
        }
        parent::tearDown();
    }

    public function testSimpleUpload()
    {
        // step 1 - init driver
        $lib1 = new UploaderMock();
        $maxSize = filesize($this->getTestFile());
        $result1 = $lib1->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);

        // step 2 - send data
        $lib2 = new UploaderMock();
        $i = 0;
        do {
            $result2 = $lib2->upload($result1->jsonSerialize()['sharedKey'], file_get_contents(
                $this->getTestFile(), false, null, $i * $lib2->bytesPerPart, $lib2->bytesPerPart
            ));
            $i++;
        } while ($i * $lib2->bytesPerPart < $maxSize);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);

        // step 3 - close upload
        $lib3 = new UploaderMock();
        $result3 = $lib3->done($result1->jsonSerialize()['sharedKey']);

        // check content
        $this->assertTrue(file_get_contents($result3->getTargetFile()) == file_get_contents($this->getTestFile()));
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        @unlink($result3->getTargetFile());
    }
}

class UploaderMock extends Uploader
{
    public $bytesPerPart = 512;
}
