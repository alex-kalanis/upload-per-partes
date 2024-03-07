<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Response;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\Uploader\Translations;


class ResponseTest extends CommonTestClass
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testInitBegin(): void
    {
        $lib = Response\InitResponse::initOk(new Translations(), $this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals('fghjkl.partial', $lib->jsonSerialize()['name']);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(12, $lib->jsonSerialize()['totalParts']);
        $this->assertEquals(64, $lib->jsonSerialize()['partSize']);
        $this->assertEquals(7, $lib->jsonSerialize()['lastKnownPart']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInitError(): void
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\InitResponse::initError(null, $this->mockData(), $ex);

        $this->assertEquals('fghjkl.partial', $lib->jsonSerialize()['name']);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testCheckOk(): void
    {
        $lib = Response\CheckResponse::initOk(null, $this->mockSharedKey(), '123abc456def789');

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals('123abc456def789', $lib->jsonSerialize()['checksum']);
        $this->assertEquals(Response\CheckResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    public function testCheckError(): void
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\CheckResponse::initError(new Translations(), $this->mockSharedKey(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals('', $lib->jsonSerialize()['checksum']);
        $this->assertEquals(Response\CheckResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testTruncateOk(): void
    {
        $lib = Response\TruncateResponse::initOk(new Translations(), $this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(7, $lib->jsonSerialize()['lastKnownPart']);
        $this->assertEquals(Response\TruncateResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testTruncateError(): void
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\TruncateResponse::initError(null, $this->mockSharedKey(), $this->mockData(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\TruncateResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUploadOk(): void
    {
        $lib = Response\UploadResponse::initOK(null, $this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['errorMessage']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUploadFail(): void
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\UploadResponse::initError(new Translations(), $this->mockSharedKey(), $this->mockData(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testDoneComplete(): void
    {
        $data = $this->mockData();
        $lib = Response\DoneResponse::initDone(new Translations(), $this->mockSharedKey(), $data);

        $this->assertEquals('/tmp/', $lib->getTemporaryLocation());
        $this->assertEquals('fghjkl.partial', $lib->getFileName());
        $this->assertEquals(123456, $lib->getSize());
        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['errorMessage']);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testDoneFail(): void
    {
        $data = $this->mockData();
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\DoneResponse::initError(null, $this->mockSharedKey(), $data, $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testCancelOk(): void
    {
        $lib = Response\CancelResponse::initCancel(null, $this->mockSharedKey());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    public function testCancelError(): void
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\CancelResponse::initError(new Translations(), $this->mockSharedKey(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['serverData']);
        $this->assertEquals(Response\CancelResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }
}