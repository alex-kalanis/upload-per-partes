<?php

namespace kalanis\UploadPerPartes\Uploader;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;


/**
 * Class Config
 * @package kalanis\UploadPerPartes\Uploader
 * Configuration of the whole uploader
 */
final class Config
{
    /** @var int<0, max> */
    public int $bytesPerPart = 0;
    public bool $canContinue = true;
    public string $tempDir = '';
    public string $targetDir = '';
    /** @var string|Interfaces\IUppTranslations|null */
    public $lang = null;
    /** @var string|Interfaces\IOperations|null */
    public $target = null;
    /** @var string|DrivingFile\DataEncoders\AEncoder|null */
    public $dataEncoder = null;
    /** @var string|DrivingFile\DataModifiers\AModifier|null */
    public $dataModifier = null;
    /** @var string|DrivingFile\KeyEncoders\AEncoder|null */
    public $keyEncoder = null;
    /** @var string|DrivingFile\KeyModifiers\AModifier|null */
    public $keyModifier = null;
    /** @var int|string|object|null */
    public $drivingFileStorage = null;
    /** @var int|string|object|null */
    public $temporaryStorage = null;
    /** @var string|Local\TemporaryStorage\KeyEncoders\AEncoder|null */
    public $temporaryEncoder = null;
    /** @var int|string|object|null */
    public $finalStorage = null;
    /** @var string|Local\FinalStorage\KeyEncoders\AEncoder|null */
    public $finalEncoder = null;
    /** @var string|null */
    public ?string $checksum = null;
    /** @var string|null */
    public ?string $decoder = null;

    /**
     * @param array<string, string|int|bool|object|array<string|int|bool|object>|null> $params
     */
    public function __construct(array $params)
    {
        if (isset($params['calc_size'])) {
            $this->bytesPerPart = max(1, intval($params['calc_size']));
        }
        if (isset($params['temp_location'])) {
            $this->tempDir = strval($params['temp_location']);
        }
        if (isset($params['target_location'])) {
            $this->targetDir = strval($params['target_location']);
        }
        if (isset($params['lang'])) {
            if (is_object($params['lang'])) {
                if ($params['lang'] instanceof Interfaces\IUppTranslations) {
                    $this->lang = $params['lang'];
                }
            } else {
                $this->lang = strval($params['lang']);
            }
        }
        if (isset($params['target'])) {
            if (is_object($params['target'])) {
                if ($params['target'] instanceof Interfaces\IOperations) {
                    $this->target = $params['target'];
                }
            } else {
                $this->target = strval($params['target']);
            }
        }
        if (isset($params['data_encoder'])) {
            if (is_object($params['data_encoder'])) {
                if ($params['data_encoder'] instanceof DrivingFile\DataEncoders\AEncoder) {
                    $this->dataEncoder = $params['data_encoder'];
                }
            } else {
                $this->dataEncoder = strval($params['data_encoder']);
            }
        }
        if (isset($params['data_modifier'])) {
            if (is_object($params['data_modifier'])) {
                if ($params['data_modifier'] instanceof DrivingFile\DataModifiers\AModifier) {
                    $this->dataModifier = $params['data_modifier'];
                }
            } else {
                $this->dataModifier = strval($params['data_modifier']);
            }
        }
        if (isset($params['key_encoder'])) {
            if (is_object($params['key_encoder'])) {
                if ($params['key_encoder'] instanceof DrivingFile\KeyEncoders\AEncoder) {
                    $this->keyEncoder = $params['key_encoder'];
                }
            } else {
                $this->keyEncoder = strval($params['key_encoder']);
            }
        }
        if (isset($params['key_modifier'])) {
            if (is_object($params['key_modifier'])) {
                if ($params['key_modifier'] instanceof DrivingFile\KeyModifiers\AModifier) {
                    $this->keyModifier = $params['key_modifier'];
                }
            } else {
                $this->keyModifier = strval($params['key_modifier']);
            }
        }
        if (isset($params['driving_file'])) {
            if (!is_array($params['driving_file']) && !is_bool($params['driving_file'])) {
                $this->drivingFileStorage = $params['driving_file'];
            }
        }
        if (isset($params['temp_storage'])) {
            if (!is_array($params['temp_storage']) && !is_bool($params['temp_storage'])) {
                $this->temporaryStorage = $params['temp_storage'];
            }
        }
        if (isset($params['temp_encoder'])) {
            if (is_object($params['temp_encoder'])) {
                if ($params['temp_encoder'] instanceof Local\TemporaryStorage\KeyEncoders\AEncoder) {
                    $this->temporaryEncoder = $params['temp_encoder'];
                }
            } else {
                $this->temporaryEncoder = strval($params['temp_encoder']);
            }
        }
        if (isset($params['final_storage'])) {
            if (!is_array($params['final_storage']) && !is_bool($params['final_storage'])) {
                $this->finalStorage = $params['final_storage'];
            }
        }
        if (isset($params['final_encoder'])) {
            if (is_object($params['final_encoder'])) {
                if ($params['final_encoder'] instanceof Local\FinalStorage\KeyEncoders\AEncoder) {
                    $this->finalEncoder = $params['final_encoder'];
                }
            } else {
                $this->finalEncoder = strval($params['final_encoder']);
            }
        }
        if (isset($params['checksum'])) {
            $this->checksum = strval($params['checksum']);
        }
        if (isset($params['decoder'])) {
            $this->decoder = strval($params['decoder']);
        }
        $this->canContinue = isset($params['can_continue']) ? boolval(intval(strval($params['can_continue']))) : true;
    }
}
