<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Uploader;
use kalanis\UploadPerPartes\UploadException;


class UploaderTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInitOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->init('foo', 'bar', 123, 'roundabout');
        /** @var Responses\InitResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);

        $this->assertEquals('bar', $data->name);
        $this->assertEquals(999, $data->totalParts);
        $this->assertEquals(666, $data->lastKnownPart);
        $this->assertEquals(555, $data->partSize);
        $this->assertEquals('clear', $data->encoders);
        $this->assertEquals('clear', $data->checksum);
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->init('foo', 'bar', 123, 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCheckOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->check('foo', 456, 'roundabout');
        /** @var Responses\CheckResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);

        $this->assertEquals('something', $data->checksum);
    }

    /**
     * @throws UploadException
     */
    public function testCheckFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->check('foo', 456, 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->truncateFrom('foo', 789, 'roundabout');
        /** @var Responses\LastKnownResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);

        $this->assertEquals(777, $data->lastKnown);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->truncateFrom('foo', 789, 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testUploadOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->upload('foo', 'abcdefghijklmnopqrstuvwxyz', 'roundabout');
        /** @var Responses\LastKnownResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);

        $this->assertEquals(888, $data->lastKnown);
    }

    /**
     * @throws UploadException
     */
    public function testUploadFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->upload('foo', 'abcdefghijklmnopqrstuvwxyz', 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testDoneOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->done('foo', 'roundabout');
        /** @var Responses\DoneResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);

        $this->assertEquals('bar_baz', $data->name);
    }

    /**
     * @throws UploadException
     */
    public function testDoneFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->done('foo', 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancelOk(): void
    {
        $lib = $this->getLib();
        $data = $lib->cancel('foo', 'roundabout');
        /** @var Responses\BasicResponse $data */
        $this->assertEquals('OK', $data->status);
        $this->assertEquals('OK', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancelFail(): void
    {
        $lib = $this->getLibFail();
        $data = $lib->cancel('foo', 'roundabout');
        /** @var Responses\ErrorResponse $data */
        $this->assertEquals('FAIL', $data->status);
        $this->assertEquals('mock', $data->errorMessage);
        $this->assertEquals('foo', $data->serverKey);
        $this->assertEquals('roundabout', $data->roundaboutClient);
    }

    /**
     * @throws UploadException
     * @return Uploader
     */
    protected function getLib(): Uploader
    {
        return new Uploader(null, [
            'target' => new XOper(),
        ]);
    }

    /**
     * @throws UploadException
     * @return Uploader
     */
    protected function getLibFail(): Uploader
    {
        return new Uploader(null, [
            'target' => new XOperFail(),
        ]);
    }
}


class XOper implements Interfaces\IOperations
{
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): Responses\BasicResponse
    {
        return (new Responses\InitResponse())->setPassedInitData($targetFileName, 999, 666, 555, 'clear', 'clear')->setBasics('foo', $clientData);
    }

    public function check(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        return (new Responses\CheckResponse())->setChecksum('something')->setBasics($serverData, $clientData);
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        return (new Responses\LastKnownResponse())->setLastKnown(777)->setBasics($serverData, $clientData);
    }

    public function upload(string $serverData, string $content, string $clientData = ''): Responses\BasicResponse
    {
        return (new Responses\LastKnownResponse())->setLastKnown(888)->setBasics($serverData, $clientData);
    }

    public function done(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        return (new Responses\DoneResponse())->setFinalName('bar_baz')->setBasics($serverData, $clientData);
    }

    public function cancel(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        return (new Responses\BasicResponse())->setBasics($serverData, $clientData);
    }
}


class XOperFail implements Interfaces\IOperations
{
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function check(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function upload(string $serverData, string $content, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function done(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }

    public function cancel(string $serverData, string $clientData = ''): Responses\BasicResponse
    {
        throw new UploadException('mock');
    }
}
