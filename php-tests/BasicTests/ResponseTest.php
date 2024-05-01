<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\UploadException;


class ResponseTest extends CommonTestClass
{
    public function testBasic(): void
    {
        $lib = new Responses\BasicResponse();
        $lib->setBasics($this->mockSharedKey(), 'mock data');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('mock data', $lib->roundaboutClient);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'mock data',
        ], (array) $lib);
    }

    public function testError1(): void
    {
        $lib = new Responses\ErrorResponse();
        $lib->setErrorMessage('Testing one')
            ->setBasics('shared key', 'back to client');

        $this->assertEquals('shared key', $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_FAIL, $lib->status);
        $this->assertEquals('Testing one', $lib->errorMessage);
        $this->assertEquals('back to client', $lib->roundaboutClient);

        $this->assertEquals([
            'serverKey' => 'shared key',
            'status' => Responses\BasicResponse::STATUS_FAIL,
            'errorMessage' => 'Testing one',
            'roundaboutClient' => 'back to client',
        ], (array) $lib);
    }

    public function testError2(): void
    {
        $lib = new Responses\ErrorResponse();
        $lib->setError(new UploadException('Testing two'))
            ->setBasics('shared key', 'back to client');

        $this->assertEquals('shared key', $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_FAIL, $lib->status);
        $this->assertEquals('Testing two', $lib->errorMessage);
        $this->assertEquals('back to client', $lib->roundaboutClient);

        $this->assertEquals([
            'serverKey' => 'shared key',
            'status' => Responses\BasicResponse::STATUS_FAIL,
            'errorMessage' => 'Testing two',
            'roundaboutClient' => 'back to client',
        ], (array) $lib);
    }

    public function testInit1(): void
    {
        $data = $this->mockData();
        $data->clear();

        $lib = new Responses\InitResponse();
        $lib->setInitData($data, 'packing', 'hashing')
            ->setBasics($this->mockSharedKey(), 'back to client');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('back to client', $lib->roundaboutClient);

        $this->assertEquals('abcdef', $lib->name);
        $this->assertEquals(12, $lib->totalParts);
        $this->assertEquals(7, $lib->lastKnownPart);
        $this->assertEquals(64, $lib->partSize);
        $this->assertEquals('packing', $lib->encoder);
        $this->assertEquals('hashing', $lib->check);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'back to client',
            'name' => 'abcdef',
            'totalParts' => 12,
            'lastKnownPart' => 7,
            'partSize' => 64,
            'check' => 'hashing',
            'encoder' => 'packing',
        ], (array) $lib);
    }

    public function testInit2(): void
    {
        $lib = new Responses\InitResponse();
        $lib->setPassedInitData(
                'foo_bar_baz',
                951,
                357,
                684,
                'packing',
                'hashing'
            )
            ->setBasics($this->mockSharedKey(), 'back to client');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('back to client', $lib->roundaboutClient);

        $this->assertEquals('foo_bar_baz', $lib->name);
        $this->assertEquals(951, $lib->totalParts);
        $this->assertEquals(357, $lib->lastKnownPart);
        $this->assertEquals(684, $lib->partSize);
        $this->assertEquals('packing', $lib->encoder);
        $this->assertEquals('hashing', $lib->check);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'back to client',
            'name' => 'foo_bar_baz',
            'totalParts' => 951,
            'lastKnownPart' => 357,
            'partSize' => 684,
            'check' => 'hashing',
            'encoder' => 'packing',
        ], (array) $lib);
    }

    public function testCheck(): void
    {
        $lib = new Responses\CheckResponse();
        $lib->setChecksum('grsd', 'abcxyz')
            ->setBasics($this->mockSharedKey(), 'mock data');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('mock data', $lib->roundaboutClient);

        $this->assertEquals('grsd', $lib->method);
        $this->assertEquals('abcxyz', $lib->checksum);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'mock data',
            'checksum' => 'abcxyz',
            'method' => 'grsd',
        ], (array) $lib);
    }

    public function testLastKnown(): void
    {
        $lib = new Responses\LastKnownResponse();
        $lib->setLastKnown(44455)
            ->setBasics($this->mockSharedKey(), 'mock data');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('mock data', $lib->roundaboutClient);

        $this->assertEquals(44455, $lib->lastKnownPart);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'mock data',
            'lastKnownPart' => 44455,
        ], (array) $lib);
    }

    public function testDone(): void
    {
        $lib = new Responses\DoneResponse();
        $lib->setFinalName('not yours data')
            ->setBasics($this->mockSharedKey(), 'mock data');

        $this->assertEquals($this->mockSharedKey(), $lib->serverKey);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->status);
        $this->assertEquals(Responses\BasicResponse::STATUS_OK, $lib->errorMessage);
        $this->assertEquals('mock data', $lib->roundaboutClient);

        $this->assertEquals('not yours data', $lib->name);

        $this->assertEquals([
            'serverKey' => $this->mockSharedKey(),
            'status' => Responses\BasicResponse::STATUS_OK,
            'errorMessage' => Responses\BasicResponse::STATUS_OK,
            'roundaboutClient' => 'mock data',
            'name' => 'not yours data',
        ], (array) $lib);
    }

    /**
     * @throws UploadException
     */
    public function testFactoryPass(): void
    {
        $lib = new Responses\Factory();
        $this->assertInstanceOf(Responses\CheckResponse::class, $lib->getResponse(Responses\Factory::RESPONSE_CHECK));
    }

    /**
     * @throws UploadException
     */
    public function testFactoryFailBadClass(): void
    {
        $lib = new XFFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Selected bad response type.');
        $lib->getResponse('std');
    }

    /**
     * @throws UploadException
     */
    public function testFactoryFailNotClass(): void
    {
        $lib = new XFFactory();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Class "not-a-class" does not exist');
        $lib->getResponse('failed');
    }
}


class XFFactory extends Responses\Factory
{
    protected array $responses = [
        'std' => \stdClass::class,
        'failed' => 'not-a-class',
    ];
}
