<?php

namespace BasicTests;

use CommonTestClass;
use Support;
use UploadPerPartes\DataStorage;
use UploadPerPartes\Response;
use UploadPerPartes\InfoStorage;
use UploadPerPartes\Uploader;

class UploadTest extends CommonTestClass
{
    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
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
        };

        // step 3 - close upload
        /** @var Response\DoneResponse $result3 */
        $result3 = $lib->done($sharedKey);
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $uploaded = $lib->getStorage()->getAll();
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
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
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $this->assertEmpty($lib->getStorage()->getAll());
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInitFail(): void
    {
        $lib = new UploaderMock();
        // init data - but there is failure
        $result1 = $lib->init('', '', 123456);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $result1->jsonSerialize()['status']);
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
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
     * @throws \UploadPerPartes\Exceptions\UploadException
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
     * @throws \UploadPerPartes\Exceptions\UploadException
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
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testCancelFail(): void
    {
        $lib = new UploaderMock();
        // cancel data - but there is nothing
        $result2 = $lib->cancel('qwertzuiop');
        $this->assertEquals(Response\CancelResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
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
    protected function getInfoStorage(Uploader\Translations $lang): InfoStorage\AStorage
    {
        parent::getInfoStorage($lang);
        return new Support\InfoRam($lang);
    }

    protected function getDataStorage(Uploader\Translations $lang): DataStorage\AStorage
    {
        parent::getDataStorage($lang);
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
    public function getStorage(): DataStorage\AStorage
    {
        return $this->dataStorage;
    }
}
