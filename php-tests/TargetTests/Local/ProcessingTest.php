<?php

namespace TargetTests\Local;


use CommonTestClass;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Uploader;
use kalanis\UploadPerPartes\UploadException;


class ProcessingTest extends CommonTestClass
{
    /**
     * @throws StorageException
     * @throws UploadException
     */
    public function testSimpleUpload(): void
    {
        $finalStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'final_storage' => $finalStorage,
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        /** @var Responses\InitResponse $result1 */
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $bytesPerPart = $result1->partSize;
        $sharedKey = $result1->serverKey; // for this test it's zero care
        $this->assertEquals(1024, $bytesPerPart);

        // step 2 - send data
        for ($i = 0; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result2 = $lib->upload($sharedKey, $part, Local\ContentDecoders\Factory::FORMAT_RAW);
            $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result2->status);
        }

        // step 3 - close upload
        /** @var Responses\DoneResponse $result3 */
        $result3 = $lib->done($sharedKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result3->status);

        // check content
        $uploaded = $finalStorage->read($result3->name);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws StorageException
     * @throws UploadException
     */
    public function testStoppedUpload(): void
    {
        $finalStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'final_storage' => $finalStorage,
            'calc_size' => 512,
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        /** @var Responses\InitResponse $result1 */
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $bytesPerPart = $result1->partSize;
        $sharedKey = $result1->serverKey; // for this test it's zero care
        $this->assertEquals(512, $bytesPerPart);
        $this->assertEquals(1261, $result1->totalParts);
        $this->assertEquals('lorem-ipsum.txt', $result1->name);

        // step 2 - send first part of data
        $limited = floor($maxSize / 2);
        for ($i = 0; $i * $bytesPerPart < $limited; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result2 = $lib->upload($sharedKey, $part, Local\ContentDecoders\Factory::FORMAT_RAW);
            $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result2->status);
        }

        // step 3 - again from the beginning
        /** @var Responses\InitResponse $result3 */
        $result3 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result3->status);
        $bytesPerPart = $result3->partSize;
        $lastKnownPart = $result3->lastKnownPart;
        $sharedKey = $result3->serverKey; // for this test it's zero care
        $this->assertEquals('lorem-ipsum.txt', $result3->name);
        $this->assertEquals(631, $lastKnownPart); // NOT ZERO
        $this->assertEquals(512, $bytesPerPart);

        // step 4 - check first part
        for ($i = 0; $i <= $lastKnownPart; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            try {
                /** @var Responses\CheckResponse $result4 */
                $result4 = $lib->check($sharedKey, $i, 'md5');
                $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result4->status);
            } catch (UploadException $ex) {
                if (md5($part) != $result4->checksum) {
                    // step 5 - truncate of failed part
                    /** @var Responses\LastKnownResponse $result5 */
                    $result5 = $lib->truncate($sharedKey, $i - 2);
                    $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result5->status);
                    break;
                } else {
                    $this->assertEquals(md5($part), $result4->checksum);
                }
            }
        }
        if (!isset($result5)) {
            $this->assertTrue(false, 'No results');
            return;
        }
        $lastKnownPart = $result5->lastKnown;
        $this->assertEquals(629, $lastKnownPart);

        // step 6 - send second part
        for ($i = $lastKnownPart; $i * $bytesPerPart <= $maxSize; $i++) {
            $part = substr($content, $i * $bytesPerPart, $bytesPerPart);
            $result6 = $lib->upload($sharedKey, $part, Local\ContentDecoders\Factory::FORMAT_RAW);
            $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result6->status);
        }

        // step 7 - close upload
        /** @var Responses\DoneResponse $result7 */
        $result7 = $lib->done($sharedKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result7->status);

        // check content
        $uploaded = $finalStorage->read($result7->name);
        $this->assertGreaterThan(0, strlen($uploaded));
        $this->assertTrue($content == $uploaded);
    }

    /**
     * @throws StorageException
     * @throws UploadException
     */
    public function testCancel(): void
    {
        $tempStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'temp_storage' => $tempStorage,
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $result2 = $lib->upload($sharedKey, $content, Local\ContentDecoders\Factory::FORMAT_RAW); // flush it all
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result2->status);

        // step 3 - cancel upload
        /** @var Responses\BasicResponse $result3 */
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result3->status);

        // check content
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Cannot read key');
        $tempStorage->read($sharedKey);
    }

    /**
     * @throws UploadException
     */
    public function testInitRepeatDisabled(): void
    {
        $content = 'dummy_content';
        $maxSize = strlen($content);

        $lib = new Local\Processing($this->params([
            'can_continue' => false,
        ]));
        $this->assertNotEmpty($lib);
        $result1 = $lib->init('dummy_file', 'dummy_file', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The driving file *dummy_file.upload* already exists in storage.');
        $lib->init('dummy_file', 'dummy_file', $maxSize);
    }

    /**
     * @throws StorageException
     * @throws UploadException
     */
    public function testInitFileAlreadyExists(): void
    {
        $tempStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'temp_storage' => $tempStorage,
        ]));
        $this->assertNotEmpty($lib);

        $tempStorage->write('dummy_file.upload', 'something');

        $result1 = $lib->init('dummy_file', 'dummy_file', 9999);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lib = new Local\Processing($this->params());
        // init data - but there is failure
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Sent file name is empty.');
        $lib->init('', '', 123456);
    }

    /**
     * @throws UploadException
     */
    public function testCheckFail(): void
    {
        $lib = new Local\Processing($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        try {
            // step 2 - check data - non existing segment
            $lib->check($sharedKey, 9999999, 'md5');
            $this->assertTrue(false, 'Segment found out-of-bounds');
        } catch (UploadException $ex) {
            // intentionally failed
        }

        // step 3 - cancel upload
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result3->status);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $lib = new Local\Processing($this->params()); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - truncate data - non existing segment
        try {
            $lib->truncate($sharedKey, 35);
            $this->assertTrue(false, 'Segment found out-of-bounds');
        } catch (UploadException $ex) {
            // intentionally failed
        }

        // step 3 - cancel upload
        /** @var Responses\BasicResponse $result3 */
        $result3 = $lib->cancel($sharedKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result3->status);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFailStorage(): void
    {
        $tempStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'temp_storage' => new XStorageTruncateFail($tempStorage),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - truncate data - non existing segment
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot truncate file *lorem-ipsum.txt*');
        $lib->truncate($sharedKey, 0);
    }

    /**
     * @throws UploadException
     */
    public function testUploadFailStorage(): void
    {
        $tempStorage = new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory());

        $lib = new Local\Processing($this->params([
            'temp_storage' => new XStorageUploadFail($tempStorage),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *lorem-ipsum.txt*');
        $lib->upload($sharedKey, $content, Local\ContentDecoders\Factory::FORMAT_RAW); // flush it all
    }

    /**
     * @throws UploadException
     */
    public function testCancelFail(): void
    {
        $lib = new Local\Processing($this->params());
        // cancel data - but there is nothing
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot read key');
        $lib->cancel('1234567890abcdef');
    }

    /**
     * @throws UploadException
     */
    public function testCancelFailTempStorage(): void
    {
        $lib = new Local\Processing($this->params([
            'temp_storage' => new XStorageRemoveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot remove drive file for upload *lorem-ipsum.txt*');
        $lib->cancel($sharedKey); // flush it all
    }

    /**
     * @throws UploadException
     */
    public function testCancelFailInfoStorage(): void
    {
        $lib = new Local\Processing($this->params([
            'driving_file' => new XStorageRemoveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot remove drive file for upload *lorem-ipsum.txt*');
        $lib->cancel($sharedKey); // flush it all
    }

    /**
     * @throws UploadException
     */
    public function testDoneFail(): void
    {
        $lib = new Local\Processing($this->params());
        // done data - but there is nothing
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot read key');
        $lib->done('1234567890abcdef');
    }

    /**
     * @throws UploadException
     */
    public function testDoneFailFinalStorage(): void
    {
        $lib = new Local\Processing($this->params([
            'driving_file' => new XStorageRemoveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'final_storage' => new XStorageSaveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot write file *lorem-ipsum.txt*');
        $lib->done($sharedKey); // flush it all
    }

    /**
     * @throws UploadException
     */
    public function testDoneFailTempStorage(): void
    {
        $lib = new Local\Processing($this->params([
            'driving_file' => new XStorageRemoveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'final_storage' => new XStorageSavePass(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot remove drive file for upload *lorem-ipsum.txt*');
        $lib->done($sharedKey); // flush it all
    }

    /**
     * @throws UploadException
     */
    public function testDoneFailInfoStorage(): void
    {
        $lib = new Local\Processing($this->params([
            'temp_storage' => new XStorageRemoveFail(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'final_storage' => new XStorageSavePass(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
        ])); // must stay same, because it's only in the ram
        $content = file_get_contents($this->getTestFile()); // read test content into ram
        $maxSize = strlen($content);

        // step 1 - init driver
        $result1 = $lib->init($this->getTestDir(), 'lorem-ipsum.txt', $maxSize);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $result1->status);
        $sharedKey = $result1->serverKey; // for this test it's zero care

        // step 2 - send data
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Cannot remove drive file for upload *lorem-ipsum.txt*');
        $lib->done($sharedKey); // flush it all
    }

    protected function params(array $extra = []): Uploader\Config
    {
        return new Uploader\Config(array_merge([
            'temp_location' => '',
            'driving_file' => new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'data_encoder' => new Local\DrivingFile\DataEncoders\Line(),
            'data_modifier' => new Local\DrivingFile\DataModifiers\Clear(),
            'key_encoder' => new Local\DrivingFile\KeyEncoders\Name(),
            'key_modifier' => new Local\DrivingFile\KeyModifiers\Suffix(),
            'temp_storage' => new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'temp_encoder' => new Local\TemporaryStorage\KeyEncoders\Name(),
            'final_storage' => new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'final_encoder' => new Local\FinalStorage\KeyEncoders\Name(),
            'calc_size' => 1024,
            'decoder' => Local\ContentDecoders\Factory::FORMAT_RAW,
        ], $extra));
    }
}


class XStorageTruncateFail extends Local\TemporaryStorage\Storage\Storage
{
    public function truncate(string $path, int $fromByte): bool
    {
        return false;
    }
}


class XStorageUploadFail extends Local\TemporaryStorage\Storage\Storage
{
    public function append(string $path, string $content): bool
    {
        return false;
    }
}


class XStorageRemoveFail extends Storage\Storage
{
    public function remove(string $key): bool
    {
        return false;
    }
}


class XStorageSavePass extends Storage\Storage
{
    public function write(string $sharedKey, string $data, ?int $timeout = null): bool
    {
        return true;
    }
}


class XStorageSaveFail extends Storage\Storage
{
    public function write(string $sharedKey, string $data, ?int $timeout = null): bool
    {
        return false;
    }
}
