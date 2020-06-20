<?php

namespace UploadPerPartes\examples\controller;

use UploadPerPartes;

/**
 * Class Uploader
 * @package UploadPerPartes\examples\controller
 * What can be displayed on page for content upload
 * This one uses Redis as driver storage; just for fun
 */
class Uploader extends \Nette\Application\UI\Presenter //  extends \yourFavouriteFrameworkControllerClass
{
    const ENCODING_UPLOAD_PATH = '/path-to-temp';

    public function ajaxUploadPartesInit()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH); // here temp path and init everytime with it
            $this->sendResponse($lib->partesInit(
                $this->getHttpRequest()->getPost()->__get('fileName'),
                $this->getHttpRequest()->getPost()->__get('fileSize')
            ));
        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\InitResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                UploadPerPartes\DataFormat\Data::init(),
                $ex
            ));
        }
    }

    public function ajaxUploadPartesCheck()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH,
                $this->getHttpRequest()->getPost()->__get('sharedKey')
            );
            $this->sendResponse($lib->partesCheck((int)$this->getHttpRequest()->getPost()->__get('segment')));
        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\CheckResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                $ex
            ));
        }
    }

    public function ajaxUploadPartesPart()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH,
                $this->getHttpRequest()->getPost()->__get('sharedKey')
            );
            $this->sendResponse($lib->partesUpload(
                base64_decode($this->getHttpRequest()->getPost()->__get('content'))
            ));
        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\UploadResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                UploadPerPartes\DataFormat\Data::init(),
                $ex
            ));
        }
    }

    public function ajaxUploadPartesTruncate()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH,
                $this->getHttpRequest()->getPost()->__get('sharedKey')
            );
            $this->sendResponse($lib->partesTruncateFrom($this->getHttpRequest()->getPost()->__get('segment')));
        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\TruncateResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                UploadPerPartes\DataFormat\Data::init(),
                $ex
            ));
        }
    }

    public function ajaxUploadPartesCancel()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH,
                $this->getHttpRequest()->getPost()->__get('sharedKey')
            );
            $this->sendResponse($lib->partesCancel());
        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\CancelResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                $ex
            ));
        }
    }

    public function ajaxUploadPartesDone()
    {
        try {
            $lib = new UploadPerPartes\examples\Upload(static::ENCODING_UPLOAD_PATH,
                $this->getHttpRequest()->getPost()->__get('sharedKey')
            );
            $result = $lib->partesDone();

            // check uploaded content and move it on drive
            $libMove = new Lib\Content\FileSave($this->getUser());
            $uploadedContent = $libMove->checkAndMove(
                $result->getTargetFile(), // temp name
                $result->getFileName() // final name
            );
            $this->getSession()->getSection('uploader')->__set('uploadedContent', $uploadedContent->getPath()); // pass file between pages
            $this->sendResponse($result);
            // and user shall got Thanks

        } catch (UploadPerPartes\Exceptions\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\DoneResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                UploadPerPartes\DataFormat\Data::init(),
                $ex
            ));
        } catch (Lib\Content\UploadException $ex) {
            $this->sendResponse(UploadPerPartes\Response\DoneResponse::initError(
                $this->getHttpRequest()->getPost()->__get('sharedKey'),
                UploadPerPartes\DataFormat\Data::init(),
                $ex
            ));
        }
    }
}
