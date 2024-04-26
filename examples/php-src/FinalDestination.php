<?php

namespace kalanis\UploadPerPartes\examples;


use kalanis\UploadPerPartes\Interfaces\IFinalStorage;
use kalanis\UploadPerPartes\UploadException;
use Lib;


/**
 * Class FinalDestination
 * @package kalanis\UploadPerPartes\examples
 * Where to store uploaded data after the process ends
 */
class FinalDestination implements IFinalStorage
{
    protected Lib\Content\FileSave $libSave;
    protected Session $session;

    public function __construct(Lib\Content\FileSave $libSave, Session $session)
    {
        $this->libSave = $libSave;
        $this->session = $session;
    }

    public function exists(string $path): bool
    {
        return false;
    }

    public function store(string $path, $source): bool
    {
        try {
            $obtainedPath = $this->libSave->saveToDestination($path, $source);
            $this->libSave->checkSaved($obtainedPath);
            $this->session->getSection('uploader')->__set('uploadedContent', $obtainedPath); // pass file between pages
            return true;
        } catch (Lib\Content\UploadException $ex) {
            throw new UploadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function findName(string $key): string
    {
        return $key;
    }
}
