<?php

namespace kalanis\UploadPerPartes;


use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 * Main server library for drive upload per-partes
 *
 * @todo: - prehodit vyhledavani volneho souboru v initu a kopirovani v done do extra tridy,
 *          ktera bude tam a zpatky predavat data v objektu
 *          - tim se vykopne nesoumernost, kdy lookup je vpaleny v Initu, ale save je mimo Done
 *          - taky se bude snaze vracet datova polozka, protoze uz nebude potreba do ni hrabat ve volajici tride;
 *            zustane obalena uvnitr tridy
 *        - ono to v tomhle bordelu treba i nejde zretezit, tedy ze jeden upload to posila dalsimu uploadu
 *          - variace "proxy", kdy dojde jen k prostemu predani dat dal
 *          - variace "repack" kdy je ridici soubor reseny na jedne instanci a data se prebali pro dalsi;
 *            ridici soubor pak nemusi byt na cilovem stroji, kam se to posila - funguje v rezimu "clear-pass"
 *          - Uploader\DataProcessor tak dostane pristup k Init i Done, coz aktualne zamerne nema
 *
 * Stav:
 *  internal - akce  - external
 *  [lookup] : init  :
 *           : check :
 *           : trunc :
 *           :  up   :
 *           : done  : [save]
 *
 * Cil je vykopnout akce nad finalnim storage do extra tridy, takze je pujde mocknout
 *  - chci retezit uploady, takhle to jeste nejde
 *  -> myslenka je, ze upload pojede na webu v DMZ, ten to posle skrz FW a vzadu pojede druha stranka, ktera uz bude opecovavat skutecna uloziste
 *
 * todo: - kopnout do frontendu - OK
 *       - init pretazenim jede rovnou -> OK
 *       - init po vyberu jede taky rovnou - vyber je na kliknuti na dropzone -> OK
 *       - oboji vola start zpracovani; potreba zjistit tahane soubory (muze jich byt v obou pripadech zakonite vic) -> je to pricetny
 *       - font awesome je nepouzitelny, protoze potrebne ikony jsou za paywallem - udelat si vlastni -> mam
 *         - ikona "X" kdyz upload nepujde a zaroven neukazovat vyber souboru
 *         - ikona "sipka do kosiku" kdyz uploadovat lze a zaroven ukazat i vyber souboru
 */
class Uploader
{
    protected Interfaces\IUPPTranslations $lang;
    /** where to store data temporary */
    protected Interfaces\IDataStorage $uploadStorage;
    /** where to store data after end */
    protected Interfaces\IDataStorage $targetStorage;
    /** what will be new file name */
    protected ServerData\TargetFileName $targetFileName;
    /** what will be new file name */
    protected string $tempLocation = '';
    /** what will be size of segments */
    protected Uploader\CalculateSizes $calculateSize;
    /** processing with info data file */
    protected ServerData\Processor $serverData;
    protected Uploader\DataProcessor $processor;
    protected bool $canContinue = true;

    /**
     * @param array{
     *              "langs"?: object|null,
     *              "upload_storage"?: string|int|object|null,
     *              "target_storage"?: string|int|object|null,
     *              "info_format"?: string|int|object|null,
     *              "info_storage"?: string|int|object|null,
     *              "limit_data"?: string|int|object|null,
     *              "storage_key"?: string|int|object|null,
     *              "encode_key"?: string|int|object|null,
     *              "sanitize_whitespace"?: string|int|bool|null,
     *              "sanitize_alnum"?: string|int|bool|null,
     *              "calculator"?: int|object|null,
     *              "temp_location"?: string|null,
     *              "can_continue"?: bool|null
     *             } $params
     * @param ServerData\Processor|null $serverProcessor
     * @param ServerData\TargetFileName|null $targetFileName
     * @param Uploader\CalculateSizes|null $calculateSize
     * @param Uploader\CheckByHash|null $checkByHash
     * @throws Exceptions\UploadException
     */
    public function __construct(
        $params = [],
        ?ServerData\Processor $serverProcessor = null,
        ?ServerData\TargetFileName $targetFileName = null,
        ?Uploader\CalculateSizes $calculateSize = null,
        ?Uploader\CheckByHash $checkByHash = null
    )
    {
        $this->lang = !empty($params['langs']) && ($params['langs'] instanceof Interfaces\IUPPTranslations)
            ? $params['langs']
            : new Uploader\Translations();
        $this->uploadStorage = (new DataStorage\Factory($this->lang))->getFormat(
            !empty($params['upload_storage']) ? $params['upload_storage'] : new DataStorage\VolumeBasic($this->lang)
        );
        $this->targetStorage = (new DataStorage\Factory($this->lang))->getFormat(
            !empty($params['target_storage']) ? $params['target_storage'] : new DataStorage\VolumeBasic($this->lang)
        );

        $this->serverData = $serverProcessor ?: new ServerData\Processor(
            (new ServerData\DataModifiers\InfoFormatFactory($this->lang))->getFormat(
                !empty($params['info_format'])
                    ? $params['info_format']
                    : ServerData\DataModifiers\InfoFormatFactory::FORMAT_TEXT
            ),
            (new ServerData\InfoStorage\Factory($this->lang))->getFormat(
                !empty($params['info_storage'])
                    ? $params['info_storage']
                    : ServerData\InfoStorage\Volume::class
            ),
            (new ServerData\DataModifiers\LimitDataFactory($this->lang))->getVariant(
                !empty($params['limit_data'])
                    ? $params['limit_data']
                    : ServerData\DataModifiers\LimitDataFactory::VARIANT_FULL_PATH
            ),
            (new ServerData\KeyModifiers\GenerateFactory($this->lang))->getVariant(
                !empty($params['storage_key'])
                    ? $params['storage_key']
                    : ServerData\KeyModifiers\GenerateFactory::VARIANT_MD5
            ),
            (new ServerData\KeyModifiers\EncodeFactory($this->lang))->getVariant(
                !empty($params['encode_key'])
                    ? $params['encode_key']
                    : ServerData\KeyModifiers\EncodeFactory::VARIANT_HEX
            ),
            $this->lang
        );

        $this->targetFileName = $targetFileName ?: new ServerData\TargetFileName(
            $this->targetStorage,
            $this->lang,
            isset($params['sanitize_whitespace']) ? boolval(intval(strval($params['sanitize_whitespace']))) : false,
            isset($params['sanitize_alnum']) ? boolval(intval(strval($params['sanitize_alnum']))) : false
        );
        $this->calculateSize = $calculateSize ?: $this->getCalc($params);
        $this->tempLocation = $this->getTempLocation($params);
        $this->canContinue = isset($params['can_continue']) ? boolval(intval(strval($params['can_continue']))) : true;

        $this->processor = new Uploader\DataProcessor(
            $this->uploadStorage,
            $checkByHash ?: new Uploader\CheckByHash(),
            $this->lang
        );
    }

    /**
     * @param array<string, string|int|object|bool|null> $params
     * @return Uploader\CalculateSizes
     */
    protected function getCalc($params): Uploader\CalculateSizes
    {
        return !empty($params['calculator'])
            ? ((is_object($params['calculator']) && $params['calculator'] instanceof Uploader\CalculateSizes)
                ? $params['calculator']
                : (is_int($params['calculator'])
                    ? new Uploader\CalculateSizes(max(1, $params['calculator']))
                    : new Uploader\CalculateSizes(262144)
                ))
            : new Uploader\CalculateSizes(262144)
        ;
    }

    /**
     * @param array<string, string|int|object|bool|null> $params
     * @throws UploadException
     * @return string
     */
    protected function getTempLocation($params): string
    {
        if (!empty($params['temp_location'])) {
            return strval($params['temp_location']);
        }
        throw new UploadException($this->lang->uppTemporaryStorageNotSet());
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function cancel(string $serverData, string $clientData = ''): Response\AResponse
    {
        try {
            $processedInfo = $this->serverData->get($serverData);
            $this->processor->cancel($processedInfo);
            $this->serverData->remove($serverData);
            return Response\CancelResponse::initCancel(
                $this->lang,
                $serverData,
                $clientData
            );
        } catch (Exceptions\UploadException $ex) {
            return Response\CancelResponse::initError($this->lang, $serverData, $ex, $clientData);
        }
    }

    /**
     * Upload file by parts, final status
     * @param string $serverData stored string for server
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function done(string $serverData, string $clientData = ''): Response\AResponse
    {
        try {
            $processedInfo = $this->serverData->get($serverData);
            $this->serverData->remove($serverData);
            return Response\DoneResponse::initDone(
                $this->lang,
                $this->serverData->store($processedInfo, false),
                $processedInfo,
                $clientData
            );
        } catch (Exceptions\UploadException $ex) {
            return Response\DoneResponse::initError($this->lang, $serverData, ServerData\Data::init(), $ex, $clientData);
        }
    }

    /**
     * Upload file by parts, use driving file
     * @param string $serverData stored string for server
     * @param string $content binary content
     * @param int<0, max>|null $segment where it save
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function upload(string $serverData, string $content, ?int $segment = null, string $clientData = ''): Response\AResponse
    {
        try {
            $processedInfo = $this->processor->upload($this->serverData->get($serverData), $content, $segment);
            return Response\UploadResponse::initOK(
                $this->lang,
                $this->serverData->store($processedInfo),
                $processedInfo,
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\UploadResponse::initError($this->lang, $serverData, ServerData\Data::init(), $e, $clientData);
        }
    }

    /**
     * Delete problematic segments
     * @param string $serverData stored string for server
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function truncateFrom(string $serverData, int $segment, string $clientData = ''): Response\AResponse
    {
        try {
            $processedInfo = $this->processor->truncateFrom($this->serverData->get($serverData), max(0, $segment));
            return Response\TruncateResponse::initOK(
                $this->lang,
                $this->serverData->store($processedInfo),
                $processedInfo,
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\TruncateResponse::initError($this->lang, $serverData, ServerData\Data::init(), $e, $clientData);
        }
    }

    /**
     * Check already uploaded parts
     * @param string $serverData
     * @param int<0, max> $segment
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function check(string $serverData, int $segment, string $clientData = ''): Response\AResponse
    {
        try {
            $processedInfo = $this->processor->check($this->serverData->get($serverData), max(0, $segment));
            return Response\CheckResponse::initOK(
                $this->lang,
                $serverData,
                $processedInfo,
                $clientData
            );
        } catch (Exceptions\UploadException $e) {
            return Response\CheckResponse::initError($this->lang, $serverData, $e, $clientData);
        }
    }

    /**
     * Upload file by parts, create driving file
     * @param string $targetPath
     * @param string $remoteFileName posted file name
     * @param int<0, max> $length complete file size
     * @param string $clientData stored string from client
     * @return Response\AResponse
     */
    public function init(string $targetPath, string $remoteFileName, int $length, string $clientData = '̈́'): Response\AResponse
    {
        try {
            $length = max(0, $length);
            $freeEntry = ServerData\Data::init()->setData(
                $remoteFileName,
                $this->tempLocation,
                '', // init - a bit later with this object
                $targetPath,
                $this->targetFileName->process($targetPath, $remoteFileName),
                $length,
                $this->calculateSize->calcParts($length),
                $this->calculateSize->getBytesPerPart(),
                0
            );
            $freeEntry->tempName = $this->serverData->getKey($freeEntry) . ServerData\TargetFileName::FILE_UPLOAD_SUFF;

            $currentKey = $this->serverData->getKey($freeEntry);
            if ($this->serverData->existsByKey($currentKey)) {
                if (!$this->canContinue) {
                    throw new UploadException($this->lang->uppDriveFileAlreadyExists($currentKey));
                }
                $freeEntry = $this->serverData->getByKey($currentKey);
                $serverData = $this->serverData->store($freeEntry, false);
            } else {
                $serverData = $this->serverData->store($freeEntry);
            }

            // got paths - OK
            return Response\InitResponse::initOk(
                $this->lang,
                $serverData,
                $freeEntry,
                $clientData
            );
        } catch (Exceptions\UploadException $e) { // something go wrong
            return Response\InitResponse::initError(
                $this->lang,
                ServerData\Data::init()->setData(
                    $remoteFileName, '', '', '', '', $length, 0, 0
                ),
                $e,
                $clientData
            );
        }
    }
}
