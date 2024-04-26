<?php

namespace TargetTests\Remote;


use CommonTestClass;
use Furious\Psr7\Request;
use Furious\Psr7\Response;
use kalanis\UploadPerPartes\Responses\Factory;
use kalanis\UploadPerPartes\Target\Remote\Config;
use kalanis\UploadPerPartes\Target\Remote\Psr;
use kalanis\UploadPerPartes\UploadException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class PsrTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testInitOk(): void
    {
        $response = $this->getLib()->init('foo_bar', 'baz_foo', 357159, 'my roundabout');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testInitFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->init('foo_bar', 'baz_foo', 357159, 'my roundabout');
    }

    /**
     * @throws UploadException
     */
    public function testCheckOk(): void
    {
        $response = $this->getLib()->check('whatever', 1453, 'my roundabout 2');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 2', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCheckFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->check('whatever', 1453, 'my roundabout 2');
    }

    /**
     * @throws UploadException
     */
    public function testTruncateOk(): void
    {
        $response = $this->getLib()->truncate('whatever', 1453, 'my roundabout 3');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 3', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testTruncateFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->truncate('whatever', 1453, 'my roundabout 3');
    }

    /**
     * @throws UploadException
     */
    public function testUploadOk(): void
    {
        $response = $this->getLib()->upload('whatever', 'abcdef159ghijkl357mnopqr183stuvwx0yz', 'my roundabout 4');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 4', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testUploadFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->upload('whatever', 'abcdef159ghijkl357mnopqr183stuvwx0yz', 'my roundabout 4');
    }

    /**
     * @throws UploadException
     */
    public function testDoneOk(): void
    {
        $response = $this->getLib()->done('whatever', 'my roundabout 5');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 5', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testDoneFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->done('whatever', 'my roundabout 5');
    }

    /**
     * @throws UploadException
     */
    public function testCancelOk(): void
    {
        $response = $this->getLib()->cancel('whatever', 'my roundabout 6');
        $this->assertEquals('my_server', $response->serverKey);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals('my roundabout 6', $response->roundaboutClient);
    }

    /**
     * @throws UploadException
     */
    public function testCancelFail(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('mock');
        $this->getLibFail()->cancel('whatever', 'my roundabout 6');
    }

    protected function getLib(): Psr
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

        return new Psr(
            new XPsrClient(),
            new Psr\Request(New Request('POST', ''), $conf),
            new Psr\Response(new Factory())
        );
    }

    protected function getLibFail(): Psr
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

        return new Psr(
            new XPsrFailClient(),
            new Psr\Request(New Request('POST', ''), $conf),
            new Psr\Response(new Factory())
        );
    }
}


class XPsrClient implements ClientInterface
{
    /**
     * @param RequestInterface $request
     * @throws XException
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return new Response(599, [], $this->sender(strval($request->getUri())));
    }

    /**
     * @param string $path
     * @throws XException
     * @return string
     */
    protected function sender(string $path): string
    {
        switch ($path) {
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
                throw new XException('Unknown target!');
        }
    }
}


class XPsrFailClient implements ClientInterface
{
    /**
     * @param RequestInterface $request
     * @throws XException
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        throw new XException('mock');
    }
}


class XException extends \Exception implements ClientExceptionInterface
{}
