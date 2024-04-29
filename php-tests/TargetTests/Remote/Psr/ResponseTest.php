<?php

namespace TargetTests\Remote\Psr;


use CommonTestClass;
use Furious\Psr7\Response;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Target\Remote;
use kalanis\UploadPerPartes\UploadException;


class ResponseTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testFailedDecode(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Syntax error');
        $this->getLib()->init($this->getDataResponse("*not-a-json-string"), 'whatever');
    }

    /**
     * @throws UploadException
     */
    public function testInitOkNoData(): void
    {
        $lib = $this->getLib()->init($this->getDataResponse('{"status":"OK","message":"OK"}'), 'pass back');
        /** @var Responses\InitResponse $lib */
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals('', $lib->name);
        $this->assertEquals(0, $lib->totalParts);
        $this->assertEquals(0, $lib->lastKnownPart);
        $this->assertEquals(0, $lib->partSize);
        $this->assertEquals('base64', $lib->encoders);
        $this->assertEquals('md5', $lib->check);
    }

    /**
     * @throws UploadException
     */
    public function testInitOkFilledData(): void
    {
        $lib = $this->getLib()->init($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","name":"which one","totalParts":813143,"lastKnownPart":54531,"partSize":1853,"encoders":"code","check":"sum"}'), 'pass back');
        /** @var Responses\InitResponse $lib */
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals('which one', $lib->name);
        $this->assertEquals(813143, $lib->totalParts);
        $this->assertEquals(54531, $lib->lastKnownPart);
        $this->assertEquals(1853, $lib->partSize);
        $this->assertEquals('code', $lib->encoders);
        $this->assertEquals('sum', $lib->check);
    }

    /**
     * @throws UploadException
     */
    public function testInitFailInfo(): void
    {
        $lib = $this->getLib()->init($this->getDataResponse('{"status":"FAIL","message":"Something happend"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('Something happend', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testInitFailNoData(): void
    {
        $lib = $this->getLib()->init($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCheckOk(): void
    {
        $lib = $this->getLib()->check($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","checksum":"blablablabla"}'), 'pass back');
        /** @var Responses\CheckResponse $lib */
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals('blablablabla', $lib->checksum);
    }

    /**
     * @throws UploadException
     */
    public function testCheckFail(): void
    {
        $lib = $this->getLib()->check($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateOk(): void
    {
        $lib = $this->getLib()->truncate($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","lastKnown":84364}'), 'pass back');
        /** @var Responses\LastKnownResponse $lib */
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals(84364, $lib->lastKnown);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $lib = $this->getLib()->truncate($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testUploadOk(): void
    {
        $lib = $this->getLib()->upload($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","lastKnown":84364}'), 'pass back');
        /** @var Responses\LastKnownResponse $lib */
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals(84364, $lib->lastKnown);
    }

    /**
     * @throws UploadException
     */
    public function testUploadFail(): void
    {
        $lib = $this->getLib()->upload($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testDoneOk(): void
    {
        $lib = $this->getLib()->done($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","name":"ijnuhbzgvftc"}'), 'pass back');
        /** @var Responses\DoneResponse $lib */
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);

        $this->assertEquals('ijnuhbzgvftc', $lib->name);
    }

    /**
     * @throws UploadException
     */
    public function testDoneFail(): void
    {
        $lib = $this->getLib()->done($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancelOk(): void
    {
        $lib = $this->getLib()->cancel($this->getDataResponse('{"serverKey":"my_server","status":"OK","message":"OK","name":"ijnuhbzgvftc"}'), 'pass back');
        $this->assertEquals('my_server', $lib->serverKey);
        $this->assertEquals('OK', $lib->status);
        $this->assertEquals('OK', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancelFail(): void
    {
        $lib = $this->getLib()->cancel($this->getDataResponse('{"status":"FAIL"}'), 'pass back');
        $this->assertEquals('', $lib->serverKey);
        $this->assertEquals('FAIL', $lib->status);
        $this->assertEquals('', $lib->errorMessage);
        $this->assertEquals('pass back', $lib->roundaboutClient);
    }

    protected function getDataResponse(string $data): Response
    {
        return new Response(599, [], $data);
    }

    protected function getLib(): Remote\Psr\Response
    {
        return new Remote\Psr\Response(new Responses\Factory());
    }
}
