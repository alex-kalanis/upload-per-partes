<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Response;
use kalanis\UploadPerPartes\Uploader;
use Support;


class UploadTest extends CommonTestClass
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testSimpleUpload(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $bytesPerPart = $result1->jsonSerialize()['partSize'];
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care
        $this->assertEquals(1024, $bytesPerPart);

        // step 2 - send data
        for ($i = 0; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result2 = $lib->upload($sharedKey, $part);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);
        }

        // step 3 - close upload
        /** @var Response\DoneResponse $result3 */
        $target = $lib->getLibDriver()->read($sharedKey)->tempLocation;
        $result3 = $lib->done($sharedKey);
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $uploaded = $lib->getStorage()->/** @scrutinizer ignore-call */getAll($target);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testStoppedUpload(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $bytesPerPart = $result1->jsonSerialize()['partSize'];
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care
        $this->assertEquals(1024, $bytesPerPart);
        $this->assertEquals(631, $result1->jsonSerialize()['totalParts']);

        // step 2 - send first part of data
        $limited = floor($maxSize / 2);
        for ($i = 0; $i * $bytesPerPart < $limited; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result2 = $lib->upload($sharedKey, $part);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);
        }

        // step 3 - again from the beginning
        $result3 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result3->jsonSerialize()['status']);
        $bytesPerPart = $result3->jsonSerialize()['partSize'];
        $lastKnownPart = $result3->jsonSerialize()['lastKnownPart'];
        $sharedKey = $result3->jsonSerialize()['sharedKey']; // for this test it's zero care
        $this->assertEquals(316, $lastKnownPart); // NOT ZERO
        $this->assertEquals(1024, $bytesPerPart);

        // step 4 - check first part
        for ($i = 0; $i <= $lastKnownPart; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result4 = $lib->check($sharedKey, $i);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result4->jsonSerialize()['status']);
            if (md5($part) != $result4->jsonSerialize()['checksum']) {
                // step 5 - truncate of failed part
                $result5 = $lib->truncateFrom($sharedKey, $i - 2);
                $this->assertEquals(Response\UploadResponse::STATUS_OK, $result5->jsonSerialize()['status']);
                break;
            } else {
                $this->assertEquals(md5($part), $result4->jsonSerialize()['checksum']);
            }
        }
        if (!isset($result5)) {
            $this->assertTrue(false, 'No results');
            return;
        }
        $lastKnownPart = $result5->jsonSerialize()['lastKnownPart'];
        $this->assertEquals(314, $lastKnownPart);

        // step 6 - send second part
        for ($i = $lastKnownPart; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result6 = $lib->upload($sharedKey, $part);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result6->jsonSerialize()['status']);
        }

        // step 7 - close upload
        /** @var Response\DoneResponse $result3 */
        $target = $lib->getLibDriver()->read($sharedKey)->tempLocation;
        $result7 = $lib->done($sharedKey);
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result7->jsonSerialize()['status']);

        // check content
        $uploaded = $lib->getStorage()->/** @scrutinizer ignore-call */getAll($target);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCancel(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care

        // step 2 - send data
        $result2 = $lib->upload($sharedKey, $content); // flush it all
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);

        // step 3 - cancel upload
        /** @var Response\CancelResponse $result3 */
        $target = $lib->getLibDriver()->read($sharedKey)->tempLocation;
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $this->assertEmpty($lib->getStorage()->/** @scrutinizer ignore-call */getAll($target));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInitFail(): void
    {
        $lib = new UploaderMock();
        // init data - but there is failure
        $result1 = $lib->init('', '', 123456);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $result1->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCheckFail(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care

        // step 2 - check data - non existing segment
        $result2 = $lib->check($sharedKey, 35);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);

        // step 3 - cancel upload
        /** @var Response\CancelResponse $result3 */
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testTruncateFail(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care

        // step 2 - truncate data - non existing segment
        $result2 = $lib->truncateFrom($sharedKey, 35);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);

        // step 3 - cancel upload
        /** @var Response\CancelResponse $result3 */
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUploadFail(): void
    {
        $lib = new UploaderMock(); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['sharedKey']; // for this test it's zero care

        // step 2 - upload data - not continuous
        $result2 = $lib->upload($sharedKey, Support\Strings::substr($content, 23, 47564), 66);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);

        // step 3 - cancel upload
        /** @var Response\CancelResponse $result3 */
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCancelFail(): void
    {
        $lib = new UploaderMock();
        // cancel data - but there is nothing
        $result2 = $lib->cancel('qwertzuiop');
        $this->assertEquals(Response\CancelResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testDoneFail(): void
    {
        $lib = new UploaderMock();
        // done data - but there is nothing
        $result2 = $lib->done('qwertzuiop');
        $this->assertEquals(Response\DoneResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }
}


class UploaderMock extends Uploader
{
    protected function getInfoStorage(?Interfaces\IUPPTranslations $lang = null): Interfaces\IInfoStorage
    {
        parent::getInfoStorage();
        return new Support\InfoRam($lang);
    }

    protected function getDataStorage(?Interfaces\IUPPTranslations $lang = null): Interfaces\IDataStorage
    {
        parent::getDataStorage();
        return new Support\DataRam($lang);
    }

    protected function getCalc(): Uploader\Calculates
    {
        parent::getCalc();
        return new Uploader\Calculates(1024);
    }

    /**
     * @return Support\DataRam
     */
    public function getStorage(): Interfaces\IDataStorage
    {
        return $this->dataStorage;
    }

    public function getLibDriver(): Uploader\DriveFile
    {
        return $this->driver;
    }
}
