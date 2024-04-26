<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use ReflectionClass;
use ReflectionException;


/**
 * Class LangFactory
 * @package kalanis\UploadPerPartes\Uploader
 * Pass selected language in either object or class name
 */
class LangFactory
{
    protected string $uppLangVariantWrong = '';

    public function __construct(string $uppLangVariantWrong = 'Lang variant *%s* is wrong!')
    {
        $this->uppLangVariantWrong = $uppLangVariantWrong;
    }

    /**
     * @param Config $config
     * @throws UploadException
     * @return Interfaces\IUppTranslations
     */
    public function getLang(Config $config): Interfaces\IUppTranslations
    {
        $variant = $config->lang ?? new Translations();
        if (is_object($variant)) {
            return $this->checkObject($variant);
        }
        return $this->initDefined($variant);
    }

    /**
     * @param object $variant
     * @throws UploadException
     * @return Interfaces\IUppTranslations
     */
    protected function checkObject(object $variant): Interfaces\IUppTranslations
    {
        if ($variant instanceof Interfaces\IUppTranslations) {
            return $variant;
        }
        throw new UploadException(sprintf($this->uppLangVariantWrong, get_class($variant)));
    }

    /**
     * @param string $variant
     * @throws UploadException
     * @return Interfaces\IUppTranslations
     */
    protected function initDefined(string $variant): Interfaces\IUppTranslations
    {
        try {
            /** @var class-string<Interfaces\IUppTranslations> $variant */
            $ref = new ReflectionClass($variant);
            if ($ref->isInstantiable()) {
                return $this->checkObject($ref->newInstance());
            }
            throw new UploadException(sprintf($this->uppLangVariantWrong, $variant));
        } catch (ReflectionException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
