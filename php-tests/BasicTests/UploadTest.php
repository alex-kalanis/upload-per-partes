<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Response;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader;
use Support;
use TraitsTests\XTrans;


class UploadTest extends CommonTestClass
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testSimpleUpload(): void
    {
        $lib = new UploaderMock($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $bytesPerPart = $result1->jsonSerialize()['partSize'];
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care
        $this->assertEquals(1024, $bytesPerPart);

        // step 2 - send data
        for ($i = 0; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result2 = $lib->upload($sharedKey, $part);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);
        }

        // step 3 - close upload
        /** @var Response\DoneResponse $result3 */
        $target = $lib->getLibServerData()->get($sharedKey);
        $result3 = $lib->done($sharedKey);
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $uploaded = $lib->getStorage()->/** @scrutinizer ignore-call */getAll($target->tempDir . $target->tempName);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testStoppedUpload(): void
    {
        $lib = new UploaderMock(array_merge($this->params(), [
            'calculator' => 512,
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $bytesPerPart = $result1->jsonSerialize()['partSize'];
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care
        $this->assertEquals(512, $bytesPerPart);
        $this->assertEquals(1261, $result1->jsonSerialize()['totalParts']);
        $this->assertEquals('lorem-ipsum.txt', $result1->jsonSerialize()['name']);

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
        $sharedKey = $result3->jsonSerialize()['serverData']; // for this test it's zero care
        $this->assertEquals('lorem-ipsum.txt', $result3->jsonSerialize()['name']);
        $this->assertEquals(631, $lastKnownPart); // NOT ZERO
        $this->assertEquals(512, $bytesPerPart);

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
        $this->assertEquals(629, $lastKnownPart);

        // step 6 - send second part
        for ($i = $lastKnownPart; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result6 = $lib->upload($sharedKey, $part);
            $this->assertEquals(Response\UploadResponse::STATUS_OK, $result6->jsonSerialize()['status']);
        }

        // step 7 - close upload
        /** @var Response\DoneResponse $result3 */
        $target = $lib->getLibServerData()->get($sharedKey);
        $result7 = $lib->done($sharedKey);
        $this->assertEquals(Response\DoneResponse::STATUS_OK, $result7->jsonSerialize()['status']);

        // check content
        $uploaded = $lib->getStorage()->/** @scrutinizer ignore-call */getAll($target->tempDir . $target->tempName);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCancel(): void
    {
        $lib = new UploaderMock($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care

        // step 2 - send data
        $result2 = $lib->upload($sharedKey, $content); // flush it all
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $result2->jsonSerialize()['status']);

        // step 3 - cancel upload
        /** @var Response\CancelResponse $result3 */
        $target = $lib->getLibServerData()->get($sharedKey);
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $result3->jsonSerialize()['status']);

        // check content
        $this->assertEmpty($lib->getStorage()->/** @scrutinizer ignore-call */getAll($target->tempDir . $target->tempName));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCreateFailLocation(): void
    {
        $this->expectExceptionMessage('TEMPORARY STORAGE NOT SET');
        $this->expectException(Exceptions\UploadException::class);
        new UploaderMock([
            // no temp path
            'info_storage' => Support\InfoRam::class,
            'upload_storage' => Support\DataRam::class,
            'target_storage' => Support\DataRam::class,
        ]);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCreateOtherOptions(): void
    {
        $this->assertNotEmpty(new UploaderMock([
            'temp_location' => '/tmp/',
            'langs' => new XTrans(),
            'calculator' => 1024,
            'info_format' => 4, // line
            'limit_data' => 1, // name
            'storage_key' => 5, // hex
            'encode_key' => 5, // hex
            'can_continue' => false,
        ]));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInitRepeatDisabled(): void
    {
        $content = 'dummy_content';
        $maxSize = strlen($content);

        $lib = new UploaderMock(array_merge($this->params(), [
            'can_continue' => false,
            'limit_data' => 1, // name
        ]));
        $this->assertNotEmpty($lib);
        $result1 = $lib->init('dummy_file', 'dummy_file', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $result2 = $lib->init('dummy_file', 'dummy_file', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInitFail(): void
    {
        $lib = new UploaderMock($this->params());
        // init data - but there is failure
        $result1 = $lib->init('', '', 123456);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $result1->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCheckFail(): void
    {
        $lib = new UploaderMock($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care

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
        $lib = new UploaderMock($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care

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
        $lib = new UploaderMock($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $result1->jsonSerialize()['status']);
        $sharedKey = $result1->jsonSerialize()['serverData']; // for this test it's zero care

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
        $lib = new UploaderMock($this->params());
        // cancel data - but there is nothing
        $result2 = $lib->cancel('1234567890abcdef');
        $this->assertEquals(Response\CancelResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testDoneFail(): void
    {
        $lib = new UploaderMock($this->params());
        // done data - but there is nothing
        $result2 = $lib->done('1234567890abcdef');
        $this->assertEquals(Response\DoneResponse::STATUS_FAIL, $result2->jsonSerialize()['status']);
    }

    protected function params(): array
    {
        return [
            'temp_location' => '/tmp/',
            'format' => ServerData\DataModifiers\Line::class,
            'info_storage' => Support\InfoRam::class,
            'upload_storage' => Support\DataRam::class,
            'target_storage' => Support\DataRam::class,
            'calculator' => new Uploader\CalculateSizes(1024),
        ];
    }
}


class UploaderMock extends Uploader
{
    /**
     * @return Support\DataRam
     * @todo: tohle rika, ze je to spatne - nemelo by to byt potreba
     */
    public function getStorage(): Interfaces\IDataStorage
    {
        return $this->uploadStorage;
    }

    public function getLibServerData(): ServerData\Processor
    {
        return $this->serverData;
    }
}
