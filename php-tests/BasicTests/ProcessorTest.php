<?php

namespace BasicTests;


use CommonTestClass;
use Support;
use kalanis\UploadPerPartes\DataStorage;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\InfoStorage;
use kalanis\UploadPerPartes\Uploader;


class ProcessorTest extends CommonTestClass
{
    /** @var InfoStorage\AStorage|null */
    protected $infoStorage = null;
    /** @var DataStorage\AStorage|null */
    protected $dataStorage = null;
    /** @var Uploader\DriveFile|null */
    protected $driveFile = null;
    /** @var Uploader\Processor|null */
    protected $processor = null;

    public function tearDown(): void
    {
        $this->initProcessor();
        if ($this->driveFile->exists($this->mockKey())) {
            $this->driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testInit(): void
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 5;
        $data = $this->processor->init($pack, $this->mockSharedKey());

        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Data', $data);
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(5, $data->lastKnownPart);

        $data2 = $this->processor->done($this->mockKey());
        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Data', $data2);
        $this->assertEquals('abcdef', $data2->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data2->tempLocation);
        $this->assertEquals(123456, $data2->fileSize);
        $this->assertEquals(12, $data2->partsCount);
        $this->assertEquals(64, $data2->bytesPerPart);
        $this->assertEquals(5, $data2->lastKnownPart);
    }

    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testInitFail(): void
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 4;
        $data = $this->processor->init($pack, $this->mockSharedKey());

        $this->assertInstanceOf('\kalanis\UploadPerPartes\InfoFormat\Data', $data);
        $this->assertEquals(4, $data->lastKnownPart);

        $pack->lastKnownPart = 8;
        $data2 = $this->processor->init($pack, $this->mockSharedKey());
        $this->assertEquals(4, $data2->lastKnownPart);
        $this->assertNotEquals(8, $data2->lastKnownPart);
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage READ TOO EARLY
     */
    public function testUploadEarly(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack, $this->mockSharedKey());
        $datacont = 'asdfghjklyxcvbnmqwertzuiop1234567890';
        $this->processor->upload($this->mockSharedKey(), $datacont, 5); // pass, last is 4, wanted 5
        $this->processor->upload($this->mockSharedKey(), $datacont, 7); // fail, last is 5, wanted 6
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SEGMENT OUT OF BOUNDS
     */
    public function testCheckSegmentSubZero(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack, $this->mockSharedKey());
        $this->processor->check($this->mockSharedKey(), -5); // fail, sub zero
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SEGMENT OUT OF BOUNDS
     */
    public function testCheckSegmentAvailableParts(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack, $this->mockSharedKey());
        $this->processor->check($this->mockSharedKey(), 10); // fail, out of size
    }

    /**
     * @expectedException  \kalanis\UploadPerPartes\Exceptions\UploadException
     * @expectedExceptionMessage SEGMENT NOT UPLOADED YET
     */
    public function testCheckSegmentNotUploaded(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack, $this->mockSharedKey());
        $this->processor->check($this->mockSharedKey(), 6); // fail, outside upload
    }

    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testSimpleThru(): void
    {
        $this->initProcessor();
        $cont = file_get_contents($this->getTestFile(), false, null, 0, 80);
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $data = $this->processor->init($pack, $this->mockSharedKey());
        $dataCont = Support\Strings::substr($cont, 0, 30, '') . 'asdfghjklyxcvbnmqwer';
        // set problematic content
        $this->processor->upload($this->mockSharedKey(), $dataCont);
        // now checks
        for ($i = 0; $i < $data->lastKnownPart; $i++) {
            $remoteMd5 = $this->processor->check($this->mockSharedKey(), $i);
            $localMd5 = md5(Support\Strings::substr($cont, $i * $data->bytesPerPart, $data->bytesPerPart, ''));
            if ($remoteMd5 != $localMd5) {
                $this->processor->truncateFrom($this->mockSharedKey(), $i);
                break;
            }
        }
        $data = $this->driveFile->read($this->mockSharedKey());
        $this->assertEquals(3, $data->lastKnownPart);
        // set rest
        for ($i = $data->lastKnownPart + 1; $i <= $data->partsCount; $i++) {
            $dataPack = Support\Strings::substr($cont, $i * $data->lastKnownPart, $data->bytesPerPart, '');
            $this->processor->upload($this->mockSharedKey(), $dataPack);
        }
        $this->processor->cancel($this->mockSharedKey()); // intended, because pass will be checked in upload itself
    }

    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testSimpleAll(): void
    {
        $this->initProcessor();
        $cont = file_get_contents($this->getTestFile(), false, null, 0, 80);
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 7;
        $pack->partsCount = 8;
        $this->processor->init($pack, $this->mockSharedKey());
        $this->processor->upload($this->mockSharedKey(), $cont);
        $data = $this->processor->done($this->mockSharedKey());
        $this->assertEquals(8, $data->lastKnownPart);
        $this->assertEquals(8, $data->partsCount);
    }

    protected function mockKey(): string
    {
        return 'fghjkl' . Uploader\TargetSearch::FILE_DRIVER_SUFF;
    }

    protected function initProcessor(): void
    {
        $lang = Uploader\Translations::init();
        $target = new Uploader\TargetSearch($lang);
        $key = new Support\Key($lang, $target);
        $format = new InfoFormat\Json();
        $hashed = Uploader\Hashed::init();
        $this->infoStorage = new Support\InfoRam($lang);
        $this->dataStorage = new Support\DataRam($lang);
        $this->driveFile = new Uploader\DriveFile($lang, $this->infoStorage, $format, $key);
        $this->processor = new Uploader\Processor($lang, $this->driveFile, $this->dataStorage, $hashed);
    }
}