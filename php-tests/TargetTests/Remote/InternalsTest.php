<?php

namespace TargetTests\Remote;


use CommonTestClass;
use kalanis\UploadPerPartes\Responses\Factory;
use kalanis\UploadPerPartes\Target\Remote\Config;
use kalanis\UploadPerPartes\Target\Remote\Internals;
use kalanis\UploadPerPartes\UploadException;


class InternalsTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInit(): void
    {
        $response = $this->getLib()->init('foo_bar', 'baz_foo', 357159, 'my roundabout');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCheck(): void
    {
        $response = $this->getLib()->check('whatever', 1453, 'my roundabout 2');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 2', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testTruncate(): void
    {
        $response = $this->getLib()->truncate('whatever', 1453, 'my roundabout 3');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 3', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testUpload(): void
    {
        $response = $this->getLib()->upload('whatever', 'abcdef159ghijkl357mnopqr183stuvwx0yz', 'my roundabout 4');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 4', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testDone(): void
    {
        $response = $this->getLib()->done('whatever', 'my roundabout 5');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 5', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancel(): void
    {
        $response = $this->getLib()->cancel('whatever', 'my roundabout 6');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 6', $response->roundaboutClient);
    }

    protected function getLib(): Internals
    {
        $conf = new Config();
        $conf->initPath = 'start';
        $conf->checkPath = 'tell';
        $conf->truncatePath = 'cut';
        $conf->uploadPath = 'run';
        $conf->donePath = 'finish';
        $conf->cancelPath = 'storno';

        $conf->targetHost = '';
        $conf->targetPort = 0;
        $conf->pathPrefix = '';

        return new Internals(
            new XInternalsClient(),
            new Internals\Request($conf, new Internals\Data()),
            new Internals\Response(new Factory())
        );
    }
}


class XInternalsClient extends Internals\Client
{
    public function request(Internals\Data $data): string
    {
        switch ($data->path) {
            case 'start':
                return '{"serverKey":"my_server","status":"OK","message":"OK","name":"which one","totalParts":813143,"lastKnownPart":54531,"partSize":1853,"encoders":"code","checksum":"sum"}';
            case 'tell':
                return '{"serverKey":"my_server","status":"OK","message":"OK","checksum":"blablablabla"}';
            case 'cut':
                return '{"serverKey":"my_server","status":"OK","message":"OK","lastKnown":84364}';
            case 'run':
                return '{"serverKey":"my_server","status":"OK","message":"OK","lastKnown":84364}';
            case 'finish':
                return '{"serverKey":"my_server","status":"OK","message":"OK","name":"ijnuhbzgvftc"}';
            case 'storno':
                return '{"serverKey":"my_server","status":"OK","message":"OK","name":"ijnuhbzgvftc"}';
            case 'error':
                return '{"status":"FAIL","message":"Something happend"}';
            default:
                throw new UploadException('Unknown target!');
        }
    }
}
