<?php

namespace DataStorageTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\ServerData\TargetFileName;
use kalanis\UploadPerPartes\Uploader\Translations;
use Support;


class TargetFileNameTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testFailNoRemote(): void
    {
        $lang = new Translations();
        $lib = new TargetFileName(new Support\DataRam($lang), $lang);
        $this->expectExceptionMessage('SENT FILE NAME IS EMPTY');
        $this->expectException(UploadException::class);
        $lib->process('/somewhere/', '');
    }

    /**
     * @throws UploadException
     */
    public function testFailNoTarget(): void
    {
        $lang = new Translations();
        $lib = new TargetFileName(new Support\DataRam($lang), $lang);
        $this->expectExceptionMessage('TARGET DIR IS NOT SET');
        $this->expectException(UploadException::class);
        $lib->process('', 'somefile.txt');
    }

    /**
     * @throws UploadException
     */
    public function testProcessClear(): void
    {
        $lang = new Translations();
        $lib = new TargetFileName(new Support\DataRam($lang), $lang);
        $this->assertEquals('what_can_be_found.here', $lib->process($this->getTestDir(), 'what can be found$.here'));
    }

    /**
     * @throws UploadException
     */
    public function testProcessNoClear(): void
    {
        $lang = new Translations();
        $lib = new TargetFileName(new Support\DataRam($lang), $lang, false, false);
        $this->assertEquals('what el$e can be found', $lib->process($this->getTestDir(), 'what el$e can be found'));
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
        $lib = new TargetFileName($dataRam, $lang, false, false);
        $this->assertEquals('dummyFile.3.tst', $lib->process($this->getTestDir(), 'dummyFile.tst'));
    }
}
