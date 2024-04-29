<?php

namespace TargetTests\Remote;


use CommonTestClass;
use Furious\Psr7\Request;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses\BasicResponse;
use kalanis\UploadPerPartes\Target;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\Uploader\LangFactory;
use kalanis\UploadPerPartes\UploadException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class FactoryTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testPresetOk(): void
    {
        $conf = new Config([]);
        $conf->target = new XOper();
        $lib = new Target\Factory(new LangFactory(), null);
        $this->assertInstanceOf(XOper::class, $lib->getTarget($conf));
    }

    /**
     * @throws UploadException
     */
    public function testPresetClassFail(): void
    {
        $conf = new Config([]);
        $conf->target = new \stdClass();
        $lib = new Target\Factory(new LangFactory(), null);
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The target is set in a wrong way. Cannot determine it. *stdClass*');
        $lib->getTarget($conf);
    }

    /**
     * @throws UploadException
     */
    public function testPresetKeyFail(): void
    {
        $conf = new Config([]);
        $conf->target = 9999;
        $lib = new Target\Factory(new LangFactory(), null);
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('The target is not set.');
        $lib->getTarget($conf);
    }

    /**
     * @throws UploadException
     */
    public function testLocal(): void
    {
        $conf = new Config([]);
        // to set target to local
        $conf->target = 'local/dir/foo/bar';
        // to config local
        $conf->drivingFileStorage = 1;
        $conf->keyModifier = 1;
        $conf->keyEncoder = 1;
        $conf->dataModifier = 1;
        $conf->dataEncoder = 1;
        $conf->temporaryStorage = 'somewhere';
        $conf->temporaryEncoder = '1';
        $conf->finalStorage = 'somewhere';
        $conf->finalEncoder = '1';
        $lib = new Target\Factory(new LangFactory(), null);
        $this->assertInstanceOf(Target\Local\Processing::class, $lib->getTarget($conf));
    }

    /**
     * @throws UploadException
     */
    public function testRemoteInternal(): void
    {
        $conf = new Config([]);
        $conf->target = 'http://localhost:9999/';
        $lib = new Target\Factory(new LangFactory(), null);
        $this->assertInstanceOf(Target\Remote\Internals::class, $lib->getTarget($conf));
    }

    /**
     * @throws UploadException
     */
    public function testRemotePsr(): void
    {
        $conf = new Config([]);
        $conf->target = 'http://localhost:9999/';
        $lib = new Target\Factory(new LangFactory(), new XContainer());
        $this->assertInstanceOf(Target\Remote\Psr::class, $lib->getTarget($conf));
    }
}


class XOper implements Interfaces\IOperations
{
    public function init(string $targetPath, string $targetFileName, int $length, string $clientData = '̈́'): BasicResponse
    {
        throw new UploadException('mock');
    }

    public function check(string $serverData, int $segment, string $method, string $clientData = ''): BasicResponse
    {
        throw new UploadException('mock');
    }

    public function truncate(string $serverData, int $segment, string $clientData = ''): BasicResponse
    {
        throw new UploadException('mock');
    }

    public function upload(string $serverData, string $content, string $method, string $clientData = ''): BasicResponse
    {
        throw new UploadException('mock');
    }

    public function done(string $serverData, string $clientData = ''): BasicResponse
    {
        throw new UploadException('mock');
    }

    public function cancel(string $serverData, string $clientData = ''): BasicResponse
    {
        throw new UploadException('mock');
    }
}


class XContainer implements ContainerInterface
{
    public function get(string $id)
    {
        switch ($id) {
            case '\Psr\Http\Client\ClientInterface':
                return new XFactoryClient();
            case '\Psr\Http\Message\RequestInterface':
                return new Request('POST', '');
            default:
                throw new UploadException('mock');
        }
    }

    public function has(string $id): bool
    {
        return true;
    }
}


class XFactoryClient implements ClientInterface
{
    /**
     * @param RequestInterface $request
     * @throws UploadException
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        throw new UploadException('mock');
    }
}
