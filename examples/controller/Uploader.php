<?php

namespace kalanis\UploadPerPartes\examples\controller;


use kalanis\UploadPerPartes;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes\examples\controller
 * What can be displayed on page for content upload
 * This one uses Redis as driver storage; just for fun
 */
class Uploader extends \Nette\Application\UI\Presenter //  extends \yourFavouriteFrameworkControllerClass
{
    const TEMP_UPLOAD_PATH = '/temp/';
    const ENCODING_UPLOAD_PATH = '/path-to-upload-target-dir/';

    public function ajaxUploadPartesInit()
    {
        $lib = new UploadPerPartes\examples\Uploader($this->uploadParams()); // here temp path and init everytime with it
        $this->sendResponse($lib->init(
            static::ENCODING_UPLOAD_PATH,
            $this->getHttpRequest()->getPost()->__get('fileName'),
            $this->getHttpRequest()->getPost()->__get('fileSize'),
            $this->getHttpRequest()->getPost()->__get('clientData')
        ));
    }

    public function ajaxUploadPartesCheck()
    {
        $lib = new UploadPerPartes\examples\Uploader($this->uploadParams());
        $this->sendResponse($lib->check(
            $this->getHttpRequest()->getPost()->__get('serverData'),
            intval($this->getHttpRequest()->getPost()->__get('segment')),
            $this->getHttpRequest()->getPost()->__get('clientData')
        ));
    }

    public function ajaxUploadPartesPart()
    {
        $lib = new UploadPerPartes\examples\Uploader($this->uploadParams());
        $this->sendResponse($lib->upload(
            $this->getHttpRequest()->getPost()->__get('serverData'),
            base64_decode($this->getHttpRequest()->getPost()->__get('content')),
            null,
            $this->getHttpRequest()->getPost()->__get('clientData')
        ));
    }

    public function ajaxUploadPartesTruncate()
    {
        $lib = new UploadPerPartes\examples\Uploader($this->uploadParams());
        $this->sendResponse($lib->truncateFrom(
            $this->getHttpRequest()->getPost()->__get('serverData'),
            $this->getHttpRequest()->getPost()->__get('segment'),
            $this->getHttpRequest()->getPost()->__get('clientData')
        ));
    }

    public function ajaxUploadPartesCancel()
    {
        $lib = new UploadPerPartes\examples\Uploader($this->uploadParams());
        $this->sendResponse($lib->cancel(
            $this->getHttpRequest()->getPost()->__get('serverData'),
            $this->getHttpRequest()->getPost()->__get('clientData')
        ));
    }

    public function ajaxUploadPartesDone()
    {
        try {
            $lib = new UploadPerPartes\examples\Uploader($this->uploadParams());
            $result = $lib->done(
                $this->getHttpRequest()->getPost()->__get('serverData'),
                $this->getHttpRequest()->getPost()->__get('clientData')
            );

            // check uploaded content and move it on drive
            $libMove = new Lib\Content\FileSave($this->getUser());
            $uploadedContent = $libMove->checkAndMove(
                $result->getInfoData()->tempDir . $result->getInfoData()->tempName, // temp name
                $result->getInfoData()->targetDir . $result->getInfoData()->finalName // final name
            );
            $this->getSession()->getSection('uploader')->__set('uploadedContent', $uploadedContent->getPath()); // pass file between pages
            $this->sendResponse($result);
            // and user shall got Thanks

        } catch (Lib\Content\UploadException | UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\DoneResponse::initError(
                null,
                $this->getHttpRequest()->getPost()->__get('serverData'),
                UploadPerPartes\ServerData\Data::init(),
                $ex,
                $this->getHttpRequest()->getPost()->__get('clientData')
            ));
        }
    }

    protected function uploadParams(): array
    {
        return [
            'temp_location' => static::TEMP_UPLOAD_PATH,
        ];
    }
}
