<?php

namespace kalanis\UploadPerPartes\examples\controller;


use kalanis\UploadPerPartes;
use Lib;


/**
 * Class NetteUploader
 * @package kalanis\UploadPerPartes\examples\controller
 * What can be displayed on page for content upload
 * This one uses Redis as driver storage; just for fun
 */
class NetteUploader extends \Nette\Application\UI\Presenter //  extends \yourFavouriteFrameworkControllerClass
{
    const TARGET_UPLOAD_PATH = '/path-to-upload-target-dir/';

    /** @var UploadPerPartes\examples\Uploader */
    protected UploadPerPartes\examples\Uploader $uploader;
    /** @inject */
    public Redis $redis;

    public function startup(): void
    {
        parent::startup();
        $this->uploader = new UploadPerPartes\examples\Uploader(
            new UploadPerPartes\examples\Psr11ContainerAdapter($this->context),
            [
            'temp_location' => '/temp/',
            'driving_file' => new \kalanis\UploadPerPartes\Target\Local\DrivingFile\Storage\Redis($this->redis),
            'final_storage' => new \kalanis\UploadPerPartes\examples\FinalDestination(
                new Lib\Content\FileSave($this->getUser()), $this->getSession()
            ),
            'calc_size' => 1048576, // segment size: 1024*1024*1 = 1M
        ]); // here temp path and init everytime with it
    }

    public function ajaxUploadPartesInit()
    {
        $this->sendJson($this->uploader->init(
            static::TARGET_UPLOAD_PATH,
            strval($this->getHttpRequest()->getPost()->__get('fileName')),
            intval(strval($this->getHttpRequest()->getPost()->__get('fileSize'))),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }

    public function ajaxUploadPartesCheck()
    {
        $this->sendJson($this->uploader->check(
            strval($this->getHttpRequest()->getPost()->__get('serverData')),
            intval(strval($this->getHttpRequest()->getPost()->__get('segment'))),
            strval($this->getHttpRequest()->getPost()->__get('method')),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }

    public function ajaxUploadPartesTruncate()
    {
        $this->sendJson($this->uploader->truncateFrom(
            strval($this->getHttpRequest()->getPost()->__get('serverData')),
            intval(strval($this->getHttpRequest()->getPost()->__get('segment'))),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }

    public function ajaxUploadPartesPart()
    {
        $this->sendJson($this->uploader->upload(
            strval($this->getHttpRequest()->getPost()->__get('serverData')),
            strval($this->getHttpRequest()->getPost()->__get('content')),
            strval($this->getHttpRequest()->getPost()->__get('method')),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }

    public function ajaxUploadPartesCancel()
    {
        $this->sendJson($this->uploader->cancel(
            strval($this->getHttpRequest()->getPost()->__get('serverData')),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }

    public function ajaxUploadPartesDone()
    {
        $this->sendJson($this->uploader->done(
            strval($this->getHttpRequest()->getPost()->__get('serverData')),
            strval($this->getHttpRequest()->getPost()->__get('clientData'))
        ));
    }
}
