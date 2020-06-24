<?php

namespace BasicTests;

use CommonTestClass;
use UploadPerPartes\Keys;
use UploadPerPartes\Response;
use UploadPerPartes\InfoStorage;
use UploadPerPartes\Uploader;
use UploadPerPartes\Uploader\Calculates;
use UploadPerPartes\Uploader\Translations;

class UploadTest extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new InfoStorage\Volume(Translations::init());
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
        $bytesPerPart = $lib2->calculations->getBytesPerPart();
        $i = 0;
        do {
            $result2 = $lib2->upload($result1->jsonSerialize()['sharedKey'], file_get_contents(
                $this->getTestFile(), false, null, $i * $bytesPerPart, $bytesPerPart
            ));
            $i++;
        } while ($i * $bytesPerPart < $maxSize);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);

        // step 3 - close upload
        $lib3 = new UploaderMock();
        $result3 = $lib3->done($result1->jsonSerialize()['sharedKey']);

        // check content
        $this->assertTrue(file_get_contents($result3->getTemporaryLocation()) == file_get_contents($this->getTestFile()));
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        @unlink($result3->getTargetFile());
    }
}

class UploaderMock extends Uploader
{
    /** @var Calculates */
    public $calculations;

    protected function getCalc(): Calculates
    {
        return new Calculates(512);
    }
}
