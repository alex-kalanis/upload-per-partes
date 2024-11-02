<?php

namespace TargetTests\Remote\Internals;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Remote\Internals;
use kalanis\UploadPerPartes\UploadException;


class ClientTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testOK(): void
    {
        $data = new Internals\RequestData();
        $data->path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'dummy.txt';
        $lib = new Internals\Client();
        $this->assertEquals('DUMMY DUMMY DUMMY', $lib->request($data)->data);
    }

    /**
     * @throws UploadException
     */
    public function testFailed(): void
    {
        $data = new Internals\RequestData();
        $data->path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'not-exists.txt';
        $lib = new Internals\Client();
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('Bad request content');
        $lib->request($data);
    }
}
