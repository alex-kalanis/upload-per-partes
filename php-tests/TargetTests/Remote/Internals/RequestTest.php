<?php

namespace TargetTests\Remote\Internals;


use CommonTestClass;
use kalanis\UploadPerPartes\Target\Remote;


class RequestTest extends CommonTestClass
{
    public function testInit(): void
    {
        $lib = $this->getLib()->init('boo_bar_baz', 'baz_bar_foo', 123456789);
        $this->assertEquals('testing-machine:999999/nop/start', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 62',
                'timeout' => 30,
                'content' => 'targetPath=boo_bar_baz&fileName=baz_bar_foo&fileSize=123456789',
            ],
        ], $lib->context);
    }

    public function testCheck(): void
    {
        $lib = $this->getLib()->check('key', 381, 'any');
        $this->assertEquals('testing-machine:999999/nop/tell', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 37',
                'timeout' => 30,
                'content' => 'serverData=key&segment=381&method=any',
            ],
        ], $lib->context);
    }

    public function testTruncate(): void
    {
        $lib = $this->getLib()->truncate('key', 8134);
        $this->assertEquals('testing-machine:999999/nop/cut', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 27',
                'timeout' => 30,
                'content' => 'serverData=key&segment=8134',
            ],
        ], $lib->context);
    }

    public function testUpload(): void
    {
        $lib = $this->getLib()->upload('key', 'abcdefghijklmnopqrstuvwxyz0123456789', 'nop');
        $this->assertEquals('testing-machine:999999/nop/push', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 70',
                'timeout' => 30,
                'content' => 'serverData=key&content=abcdefghijklmnopqrstuvwxyz0123456789&method=nop',
            ],
        ], $lib->context);
    }

    public function testDone(): void
    {
        $lib = $this->getLib()->done('key');
        $this->assertEquals('testing-machine:999999/nop/final', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 14',
                'timeout' => 30,
                'content' => 'serverData=key',
            ],
        ], $lib->context);
    }

    public function testCancel(): void
    {
        $lib = $this->getLib()->cancel('key');
        $this->assertEquals('testing-machine:999999/nop/storno', $lib->path);
        $this->assertEquals([
            'ssl' => [
            ],
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-length: 14',
                'timeout' => 30,
                'content' => 'serverData=key',
            ],
        ], $lib->context);
    }

    protected function getLib(): Remote\Internals\Request
    {
        $conf = new Remote\Config();
        $conf->targetHost = 'testing-machine';
        $conf->targetPort = 999999;
        $conf->pathPrefix = '/nop/';

        $conf->initPath = 'start';
        $conf->checkPath = 'tell';
        $conf->truncatePath = 'cut';
        $conf->uploadPath = 'push';
        $conf->donePath = 'final';
        $conf->cancelPath = 'storno';
        return new Remote\Internals\Request($conf, new Remote\Internals\RequestData());
    }
}
