<?php

namespace StorageTests;

use CommonTestClass;
use UploadPerPartes\Exceptions\UploadException;
use UploadPerPartes\Storage\TargetSearch;
use UploadPerPartes\Uploader\Translations;

class TargetTest extends CommonTestClass
{
    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SENT FILE NAME IS EMPTY
     */
    public function testFailNoRemote()
    {
        $lib = new TargetSearch(Translations::init());
        $lib->process();
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage TARGET DIR IS NOT SET
     */
    public function testFailNoTarget()
    {
        $lib = new TargetSearch(Translations::init());
        $lib->setRemoteFileName('abcdefg');
        $lib->process();
    }

    /**
     * @expectedException \UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage UPLOAD FILE NAME IS EMPTY
     */
    public function testFailNoBase()
    {
        $lib = new TargetSearch(Translations::init());
        $lib->getFinalTargetName();
    }

    /**
     * @throws UploadException
     */
    public function testProcessClear()
    {
        $lib = new TargetSearch(Translations::init());
        $lib->setTargetDir('/tmp/')->setRemoteFileName('what can be found$.here')->process();
        $this->assertEquals('what_can_be_found.here', $lib->getFinalTargetName());
        $this->assertEquals('/tmp/what_can_be_found' . TargetSearch::FILE_DRIVER_SUFF, $lib->getDriverLocation());
        $this->assertEquals('/tmp/what_can_be_found.here' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
    }

    /**
     * @throws UploadException
     */
    public function testProcessNoClear()
    {
        $lib = new TargetSearch(Translations::init(), false, false);
        $lib->setTargetDir('/tmp/')->setRemoteFileName('what el$e can be found')->process();
        $this->assertEquals('what el$e can be found', $lib->getFinalTargetName());
        $this->assertEquals('/tmp/what el$e can be found' . TargetSearch::FILE_DRIVER_SUFF, $lib->getDriverLocation());
        $this->assertEquals('/tmp/what el$e can be found' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
    }

    /**
     * @throws UploadException
     */
    public function testProcessNameLookup()
    {
        file_put_contents('/tmp/dummyFile.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents('/tmp/dummyFile.0.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents('/tmp/dummyFile.1.tst', 'asdfghjklqwertzuiopyxcvbnm');
        file_put_contents('/tmp/dummyFile.2.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $lib = new TargetSearch(Translations::init(), false, false);
        $lib->setTargetDir('/tmp/')->setRemoteFileName('dummyFile.tst')->process();
        $this->assertEquals('/tmp/dummyFile.3.tst' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
        unlink('/tmp/dummyFile.tst');
        unlink('/tmp/dummyFile.0.tst');
        unlink('/tmp/dummyFile.1.tst');
        unlink('/tmp/dummyFile.2.tst');
    }
}