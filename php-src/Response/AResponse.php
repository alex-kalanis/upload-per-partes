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

    const STATUS_OK = 'OK';
    const STATUS_FAIL = 'FAIL';

    /** @var string */
    protected $serverData = '';
    /** @var string */
    protected $errorMessage = self::STATUS_OK;
    /** @var string */
    protected $status = self::STATUS_OK;
    /** @var string */
    protected $roundaboutServer = '';
    /** @var string */
    protected $roundaboutClient = '';

    final public function __construct(?IUPPTranslations $lang = null)
    {
        $this->setUppLang($lang);
    }
}
