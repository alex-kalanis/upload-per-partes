<?php

namespace DataStorageTests;

use CommonTestClass;
use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\DataStorage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

class TargetTest extends CommonTestClass
{
    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SENT FILE NAME IS EMPTY
     */
    public function testFailNoRemote(): void
    {
        $lib = new TargetSearch(Translations::init());
        $lib->process();
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage TARGET DIR IS NOT SET
     */
    public function testFailNoTarget(): void
    {
        $lib = new TargetSearch(Translations::init());
        $lib->setRemoteFileName('abcdefg');
        $lib->process();
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage UPLOAD FILE NAME IS EMPTY
     */
    public function testFailNoBase(): void
    {
        $lib = new TargetSearch(Translations::init());
        $lib->getFinalTargetName();
    }

    /**
     * @throws UploadException
     */
    public function testProcessClear(): void
    {
        $lib = new TargetSearch(Translations::init());
        $lib->setTargetDir($this->getTestDir())->setRemoteFileName('what can be found$.here')->process();
        $this->assertEquals('what_can_be_found.here', $lib->getFinalTargetName());
        $this->assertEquals($this->getTestDir() . 'what_can_be_found' . TargetSearch::FILE_DRIVER_SUFF, $lib->getDriverLocation());
        $this->assertEquals($this->getTestDir() . 'what_can_be_found.here' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
    }

    /**
     * @throws UploadException
     */
    public function testProcessNoClear(): void
    {
        $lib = new TargetSearch(Translations::init(), false, false);
        $lib->setTargetDir($this->getTestDir())->setRemoteFileName('what el$e can be found')->process();
        $this->assertEquals('what el$e can be found', $lib->getFinalTargetName());
        $this->assertEquals($this->getTestDir() . 'what el$e can be found' . TargetSearch::FILE_DRIVER_SUFF, $lib->getDriverLocation());
        $this->assertEquals($this->getTestDir() . 'what el$e can be found' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
    }

    /**
     * @throws UploadException
     */
    public function testProcessNameLookup(): void
    {
        file_put_contents($this->getTestDir() . 'dummyFile.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents($this->getTestDir() . 'dummyFile.0.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents($this->getTestDir() . 'dummyFile.1.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents($this->getTestDir() . 'dummyFile.2.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $lib = new TargetSearch(Translations::init(), false, false);
        $lib->setTargetDir($this->getTestDir())->setRemoteFileName('dummyFile.tst')->process();
        $this->assertEquals($this->getTestDir() . 'dummyFile.3.tst' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
        unlink($this->getTestDir() . 'dummyFile.tst');
        unlink($this->getTestDir() . 'dummyFile.0.tst');
        unlink($this->getTestDir() . 'dummyFile.1.tst');
        unlink($this->getTestDir() . 'dummyFile.2.tst');
    }
}