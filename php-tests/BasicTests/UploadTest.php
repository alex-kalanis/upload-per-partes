<?php

use UploadPerPartes\DriveFile;
use UploadPerPartes\Response;
use UploadPerPartes\Translations;
use UploadPerPartes\Upload;

class UploadTest extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new DriveFile\Text(Translations::init(), $this->mockTestFile());
            $lib->remove();
        }
        parent::tearDown();
    }

    public function testSimpleUpload()
    {
        // step 1 - init driver
        $lib1 = new UploadMock($this->getTestDir());
        $result1 = $lib1->partesInit('lorem-ipsum.txt', filesize($this->getTestFile()));
        $this->assertEquals(Response\InitResponse::STATUS_BEGIN, $result1->jsonSerialize()['status']);

        // step 2 - send data
        $lib2 = new UploadMock($this->getTestDir(), $result1->jsonSerialize()['driver']);
        $i = 0;
        do {
            $result2 = $lib2->partesUpload(file_get_contents(
                $this->getTestFile(), false, null, $i * $lib2->bytesPerPart, $lib2->bytesPerPart
            ));
            $i++;
        } while (Response\UploadResponse::STATUS_OK == $result2->jsonSerialize()['status']);
        $this->assertEquals(Response\UploadResponse::STATUS_COMPLETE, $result2->jsonSerialize()['status']);

        // step 3 - close upload
        $lib3 = new UploadMock($this->getTestDir(), $result1->jsonSerialize()['driver']);
        $result3 = $lib3->partesDone();

        // check content
        $this->assertTrue(file_get_contents($result3->getTargetFile()) == file_get_contents($this->getTestFile()));
        $this->assertEquals(Response\DoneResponse::STATUS_COMPLETE, $result3->jsonSerialize()['status']);

        @unlink($result3->getTargetFile());
    }
}

class UploadMock extends Upload
{
    public $bytesPerPart = 512;
}