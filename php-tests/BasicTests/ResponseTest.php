<?php

namespace BasicTests;

use CommonTestClass;
use UploadPerPartes\Response;
use UploadPerPartes\Exceptions;

class ResponseTest extends CommonTestClass
{
    public function testInitBegin()
    {
        $lib = Response\InitResponse::initOk($this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals('abcdef', $lib->jsonSerialize()['name']);
        $this->assertEquals(Response\InitResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(12, $lib->jsonSerialize()['totalParts']);
        $this->assertEquals(64, $lib->jsonSerialize()['partSize']);
        $this->assertEquals(7, $lib->jsonSerialize()['lastKnownPart']);
    }

    public function testInitError()
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\InitResponse::initError($this->mockData(), $ex);

        $this->assertEquals('abcdef', $lib->jsonSerialize()['name']);
        $this->assertEquals(Response\InitResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testCheckOk()
    {
        $lib = Response\CheckResponse::initOk($this->mockSharedKey(), '123abc456def789');

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals('123abc456def789', $lib->jsonSerialize()['checksum']);
        $this->assertEquals(Response\CheckResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    public function testCheckError()
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\CheckResponse::initError($this->mockSharedKey(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals('', $lib->jsonSerialize()['checksum']);
        $this->assertEquals(Response\CheckResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testTruncateOk()
    {
        $lib = Response\TruncateResponse::initOk($this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(7, $lib->jsonSerialize()['lastKnownPart']);
        $this->assertEquals(Response\TruncateResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    public function testTruncateError()
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\TruncateResponse::initError($this->mockSharedKey(), $this->mockData(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\TruncateResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testUploadOk()
    {
        $lib = Response\UploadResponse::initOK($this->mockSharedKey(), $this->mockData());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['errorMessage']);
    }

    public function testUploadFail()
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\UploadResponse::initError($this->mockSharedKey(), $this->mockData(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testDoneComplete()
    {
        $data = $this->mockData();
        $lib = Response\DoneResponse::initDone($this->mockSharedKey(), $data);

        $this->assertEquals($this->getTestDir() . $data->fileName, $lib->getTemporaryLocation());
        $this->assertEquals('abcdef', $lib->getFileName());
        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['status']);
        $this->assertEquals(Response\UploadResponse::STATUS_OK, $lib->jsonSerialize()['errorMessage']);
    }

    public function testDoneFail()
    {
        $data = $this->mockData();
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\DoneResponse::initError($this->mockSharedKey(), $data, $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\UploadResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }

    public function testCancelOk()
    {
        $lib = Response\CancelResponse::initCancel($this->mockSharedKey());

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\CancelResponse::STATUS_OK, $lib->jsonSerialize()['status']);
    }

    public function testCancelError()
    {
        $ex = new Exceptions\UploadException('Testing one');
        $lib = Response\CancelResponse::initError($this->mockSharedKey(), $ex);

        $this->assertEquals($this->mockSharedKey(), $lib->jsonSerialize()['sharedKey']);
        $this->assertEquals(Response\CancelResponse::STATUS_FAIL, $lib->jsonSerialize()['status']);
        $this->assertEquals('Testing one', $lib->jsonSerialize()['errorMessage']);
    }
}