<?php

namespace DataStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Uploader\TargetSearch;
use kalanis\UploadPerPartes\Uploader\Translations;
use Support;


class TargetTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testFailNoRemote(): void
    {
        $lang = new Translations();
        $lib = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->process();
        $this->expectExceptionMessageMatches('SENT FILE NAME IS EMPTY');
    }

    /**
     * @throws UploadException
     */
    public function testFailNoTarget(): void
    {
        $lang = new Translations();
        $lib = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $lib->setRemoteFileName('abcdefg');
        $this->expectException(UploadException::class);
        $lib->process();
        $this->expectExceptionMessageMatches('TARGET DIR IS NOT SET');
    }

    /**
     * @throws UploadException
     */
    public function testFailNoBase(): void
    {
        $lang = new Translations();
        $lib = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
        $this->expectException(UploadException::class);
        $lib->getFinalTargetName();
        $this->expectExceptionMessageMatches('UPLOAD FILE NAME IS EMPTY');
    }

    /**
     * @throws UploadException
     */
    public function testProcessClear(): void
    {
        $lang = new Translations();
        $lib = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang);
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
        $lang = new Translations();
        $lib = new TargetSearch(new Support\InfoRam($lang), new Support\DataRam($lang), $lang, false, false);
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
        $lang = new Translations();
        $dataRam = new Support\DataRam($lang);
        $dataRam->addPart($this->getTestDir() . 'dummyFile.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $dataRam->addPart($this->getTestDir() . 'dummyFile.0.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $dataRam->addPart($this->getTestDir() . 'dummyFile.1.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $dataRam->addPart($this->getTestDir() . 'dummyFile.2.tst', 'asdfghjklqwertzuiopyxcvbnm');
        $lib = new TargetSearch(new Support\InfoRam($lang), $dataRam, $lang, false, false);
        $lib->setTargetDir($this->getTestDir())->setRemoteFileName('dummyFile.tst')->process();
        $this->assertEquals($this->getTestDir() . 'dummyFile.3.tst' . TargetSearch::FILE_UPLOAD_SUFF, $lib->getTemporaryTargetLocation());
    }
}
