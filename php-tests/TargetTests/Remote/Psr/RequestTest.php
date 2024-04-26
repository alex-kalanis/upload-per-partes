<?php

namespace TargetTests\Remote\Psr;


use CommonTestClass;
use Furious\Psr7\Request;
use kalanis\UploadPerPartes\Target\Remote;


class RequestTest extends CommonTestClass
{
    public function testInit(): void
    {
        $lib = $this->getLib()->init('boo_bar_baz', 'baz_bar_foo', 123456789);
        $this->assertEquals('//testing-machine:9999/nop/start', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('targetPath=boo_bar_baz&fileName=baz_bar_foo&fileSize=123456789', $body->getContents());
    }

    public function testCheck(): void
    {
        $lib = $this->getLib()->check('key', 381);
        $this->assertEquals('//testing-machine:9999/nop/tell', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('serverData=key&segment=381', $body->getContents());
    }

    public function testTruncate(): void
    {
        $lib = $this->getLib()->truncate('key', 8134);
        $this->assertEquals('//testing-machine:9999/nop/cut', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('serverData=key&segment=8134', $body->getContents());
    }

    public function testUpload(): void
    {
        $lib = $this->getLib()->upload('key', 'abcdefghijklmnopqrstuvwxyz0123456789');
        $this->assertEquals('//testing-machine:9999/nop/push', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('serverData=key&content=abcdefghijklmnopqrstuvwxyz0123456789', $body->getContents());
    }

    public function testDone(): void
    {
        $lib = $this->getLib()->done('key');
        $this->assertEquals('//testing-machine:9999/nop/final', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('serverData=key', $body->getContents());
    }

    public function testCancel(): void
    {
        $lib = $this->getLib()->cancel('key');
        $this->assertEquals('//testing-machine:9999/nop/storno', strval($lib->getUri()));
        $this->assertEquals([
            'Host' => ['testing-machine:9999'],
            'Content-type' => ['application/x-www-form-urlencoded'],
        ], $lib->getHeaders());

        $body = $lib->getBody();
        $body->rewind();
        $this->assertEquals('serverData=key', $body->getContents());
    }

    protected function getLib(): Remote\Psr\Request
    {
        $conf = new Remote\Config();
        $conf->targetHost = 'testing-machine';
        $conf->targetPort = 9999;
        $conf->pathPrefix = '/nop/';

        $conf->initPath = 'start';
        $conf->checkPath = 'tell';
        $conf->truncatePath = 'cut';
        $conf->uploadPath = 'push';
        $conf->donePath = 'final';
        $conf->cancelPath = 'storno';
        return new Remote\Psr\Request(new Request('POST', ''),$conf);
    }
}
