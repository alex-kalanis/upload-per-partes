<?php

namespace BasicTests;

use CommonTestClass;
use Support;
use UploadPerPartes\DataStorage;
use UploadPerPartes\InfoFormat;
use UploadPerPartes\InfoStorage;
use UploadPerPartes\Uploader\DriveFile;
use UploadPerPartes\Uploader\Processor;
use UploadPerPartes\Uploader\Translations;

class ProcessorTest extends CommonTestClass
{
    /** @var InfoStorage\AStorage|null */
    protected $infoStorage = null;
    /** @var DataStorage\AStorage|null */
    protected $dataStorage = null;
    /** @var DriveFile|null */
    protected $driveFile = null;
    /** @var Processor|null */
    protected $processor = null;

    public function tearDown()
    {
        $this->initProcessor();
        if ($this->driveFile->exists($this->mockKey())) {
            $this->driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInit()
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 5;
        $data = $this->processor->init($pack, $this->mockSharedKey());

        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Data', $data);
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(5, $data->lastKnownPart);

        $data2 = $this->processor->done($this->mockKey());
        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Data', $data2);
        $this->assertEquals('abcdef', $data2->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data2->tempLocation);
        $this->assertEquals(123456, $data2->fileSize);
        $this->assertEquals(12, $data2->partsCount);
        $this->assertEquals(64, $data2->bytesPerPart);
        $this->assertEquals(5, $data2->lastKnownPart);
    }

    /**
     * @throws \UploadPerPartes\Exceptions\UploadException
     */
    public function testInitFail()
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 4;
        $data = $this->processor->init($pack, $this->mockSharedKey());

        $this->assertInstanceOf('\UploadPerPartes\InfoFormat\Data', $data);
        $this->assertEquals(4, $data->lastKnownPart);

        $pack->lastKnownPart = 8;
        $data2 = $this->processor->init($pack, $this->mockSharedKey());
        $this->assertEquals(4, $data2->lastKnownPart);
        $this->assertNotEquals(8, $data2->lastKnownPart);
    }

    protected function mockKey(): string
    {
        return 'fghjkl' . DataStorage\TargetSearch::FILE_DRIVER_SUFF;
    }

    protected function initProcessor(): void
    {
        $lang = Translations::init();
        $target = new DataStorage\TargetSearch($lang);
        $key = new Support\Key($lang, $target);
        $format = new InfoFormat\Json();
        $this->infoStorage = new Support\InfoRam($lang);
        $this->dataStorage = new Support\DataRam($lang);
        $this->driveFile = new DriveFile($lang, $this->infoStorage, $format, $key);
        $this->processor = new Processor($lang, $this->driveFile, $this->dataStorage);
    }
}