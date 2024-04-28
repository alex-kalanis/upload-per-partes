<?php

namespace kalanis\UploadPerPartes\Target;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\Uploader\LangFactory;
use kalanis\UploadPerPartes\UploadException;
use Psr\Container\ContainerInterface;


/**
 * Class Factory
 * @package kalanis\UploadPerPartes\Target
 * Responses from server to client
 */
class Factory
{
    use TLang;

    protected LangFactory $langFactory;
    protected ?ContainerInterface $container;

    public function __construct(LangFactory $langFactory, ?ContainerInterface $container)
    {
        $this->langFactory = $langFactory;
        $this->container = $container;
    }

    /**
     * @param Config $config
     * @throws UploadException
     * @return Interfaces\IOperations
     */
    public function getTarget(Config $config): Interfaces\IOperations
    {
        $this->setUppLang($this->langFactory->getLang($config));

        if (is_object($config->target)) {
            return $this->checkObject($config->target);
        }
        if (is_string($config->target)) {
            // ok, now is that a path to a local storage or remote one?
            if (filter_var($config->target, FILTER_VALIDATE_URL)) {
                return $this->initRemote($config->target);
            } else {
                // in this case the target is locally available storage, probably the filesystem
                return new Local\Processing($config, $this->getUppLang());
            }
        }
        throw new UploadException($this->getUppLang()->uppTargetNotSet());
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IOperations
     */
    protected function checkObject(object $variant): Interfaces\IOperations
    {
        if ($variant instanceof Interfaces\IOperations) {
            return $variant;
        }
        throw new UploadException($this->getUppLang()->uppTargetIsWrong(get_class($variant)));
    }

    /**
     * @param string $url
     * @throws UploadException
     * @return Interfaces\IOperations
     */
    protected function initRemote(string $url): Interfaces\IOperations
    {
        // now - have we PSR implementation or we shall use internals?
        if (
            $this->container
            && $this->container->has('\Psr\Http\Client\ClientInterface')
            && $this->container->has('\Psr\Http\Message\RequestInterface')
        ) { // MUST be as string!
            // use psr
            /** @var \Psr\Http\Client\ClientInterface $client */
            $client = $this->container->get('\Psr\Http\Client\ClientInterface');
            /** @var \Psr\Http\Message\RequestInterface $request */
            $request = $this->container->get('\Psr\Http\Message\RequestInterface');
            return new Remote\Psr(
                $client,
                new Remote\Psr\Request(
                    $request,
                    $this->remoteConfig($url)
                ),
                new Remote\Psr\Response(new Responses\Factory($this->getUppLang()), $this->getUppLang())
            );
        } else {
            // use internals
            return new Remote\Internals(
                new Remote\Internals\Client(),
                new Remote\Internals\Request(
                    $this->remoteConfig($url),
                    new Remote\Internals\Data()
                ),
                new Remote\Internals\Response(new Responses\Factory($this->getUppLang()), $this->getUppLang())
            );
        }
    }

    /**
     * @param string $url
     * @throws UploadException
     * @return Remote\Config
     */
    protected function remoteConfig(string $url): Remote\Config
    {
        $conf = new Remote\Config();
        $parsed = parse_url($url);
        if (false === $parsed) {
            // @codeCoverageIgnoreStart
            throw new UploadException($this->getUppLang()->uppTargetIsWrong($url));
        }
        // @codeCoverageIgnoreEnd
        $parsed = (array) $parsed;
        $conf->targetHost = empty($parsed['host']) ? $conf->targetHost : strval($parsed['host']);
        $conf->targetPort = empty($parsed['port']) ? $conf->targetPort : intval($parsed['port']);
        $conf->pathPrefix = empty($parsed['path']) ? $conf->pathPrefix : strval($parsed['path']);
        return $conf;
    }
}
