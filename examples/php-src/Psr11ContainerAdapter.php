<?php

namespace kalanis\UploadPerPartes\examples;


use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundException;
use Nette\DI\Container;


/**
 * Class Psr11ContainerAdapter
 * From GIST
 * @link https://gist.github.com/dg/7f02403bd36d9d1c73802a6268a4361f
 */
class Psr11ContainerAdapter implements ContainerInterface
{
    private Container $netteContainer;

    public function __construct(Container $netteContainer)
    {
        $this->netteContainer = $netteContainer;
    }

    public function get($id)
    {
        if (!$this->netteContainer->hasService($id)) {
            throw new NotFoundException(sprintf('Service not found: %s', $id));
        }

        return $this->netteContainer->getService($id);
    }

    public function has($id): bool
    {
        return $this->netteContainer->hasService($id);
    }
}
