<?php

namespace kalanis\UploadPerPartes\Response;


use JsonSerializable;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;
use kalanis\UploadPerPartes\Traits\TData;


/**
 * Class AResponse
 * @package kalanis\UploadPerPartes\Response
 * Responses for client
 */
abstract class AResponse implements JsonSerializable
{
    use TData;

    public const STATUS_OK = 'OK';
    public const STATUS_FAIL = 'FAIL';

    protected string $serverData = '';
    protected string $errorMessage = self::STATUS_OK;
    protected string $status = self::STATUS_OK;
    protected string $roundaboutServer = '';
    protected string $roundaboutClient = '';

    final public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
