<?php

namespace BasicTests;


use CommonTestClass;
use Support;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\InfoFormat;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader;


class ProcessorTest extends CommonTestClass
{
    /** @var Interfaces\IInfoStorage|null */
    protected $infoStorage = null;
    /** @var Interfaces\IDataStorage|null */
    protected $dataStorage = null;
    /** @var Uploader\DriveFile|null */
    protected $driveFile = null;
    /** @var Uploader\Processor|null */
    protected $processor = null;

    /**
     * @throws Exceptions\UploadException
     */
    public function tearDown(): void
    {
        $this->initProcessor();
        if ($this->driveFile->exists($this->mockKey())) {
            $this->driveFile->remove($this->mockKey());
        }
        parent::tearDown();
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInit(): void
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 5;
        $data = $this->processor->init($pack);

        $this->assertInstanceOf(InfoFormat\Data::class, $data);
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempLocation);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(5, $data->lastKnownPart);

        $data2 = $this->processor->done($this->mockKey());
        $this->assertInstanceOf(InfoFormat\Data::class, $data2);
        $this->assertEquals('abcdef', $data2->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data2->tempLocation);
        $this->assertEquals(123456, $data2->fileSize);
        $this->assertEquals(12, $data2->partsCount);
        $this->assertEquals(64, $data2->bytesPerPart);
        $this->assertEquals(5, $data2->lastKnownPart);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testInitFail(): void
    {
        $this->initProcessor();

        $pack = $this->mockData();
        $pack->lastKnownPart = 4;
        $data = $this->processor->init($pack);

        $this->assertInstanceOf(InfoFormat\Data::class, $data);
        $this->assertEquals(4, $data->lastKnownPart);

        $pack->lastKnownPart = 8;
        $data2 = $this->processor->init($pack);
        $this->assertEquals(4, $data2->lastKnownPart);
        $this->assertNotEquals(8, $data2->lastKnownPart);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testUploadEarly(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack);
        $datacont = 'asdfghjklyxcvbnmqwertzuiop1234567890';
        $this->processor->upload($pack, $datacont, 5); // pass, last is 4, wanted 5
        $this->expectException(Exceptions\UploadException::class);
        $this->processor->upload($pack, $datacont, 7); // fail, last is 5, wanted 6
        $this->expectExceptionMessageMatches('READ TOO EARLY');
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCheckSegmentSubZero(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack);
        $this->expectException(Exceptions\UploadException::class);
        $this->processor->check($pack, -5); // fail, sub zero
        $this->expectExceptionMessageMatches('SEGMENT OUT OF BOUNDS');
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCheckSegmentAvailableParts(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack);
        $this->expectException(Exceptions\UploadException::class);
        $this->processor->check($pack, 10); // fail, out of size
        $this->expectExceptionMessageMatches('SEGMENT OUT OF BOUNDS');
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testCheckSegmentNotUploaded(): void
    {
        $this->initProcessor();
        $pack = $this->mockData();
        $pack->fileSize = 80;
        $pack->bytesPerPart = 10;
        $pack->lastKnownPart = 4;
        $pack->partsCount = 8;
        $this->processor->init($pack);
        $this->expectException(Exceptions\UploadException::class);
        $this->processor->check($pack, 6); // fail, outside upload
        $this->expectExceptionMessageMatches('SEGMENT NOT UPLOADED YET');
    }

    /**
     * @throws Exceptions\UploadException
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
        $data = $this->processor->init($pack);
        $dataCont = Support\Strings::substr($cont, 0, 30, '') . 'asdfghjklyxcvbnmqwer';
        // set problematic content
        $this->processor->upload($data, $dataCont);
        // now checks
        for ($i = 0; $i < $data->lastKnownPart; $i++) {
            $remoteMd5 = $this->processor->check($data, $i);
            $localMd5 = md5(Support\Strings::substr($cont, $i * $data->bytesPerPart, $data->bytesPerPart, ''));
            if ($remoteMd5 != $localMd5) {
                $this->processor->truncateFrom($data, $i);
                break;
            }
        }
        $data = $this->driveFile->read($data);
        $this->assertEquals(3, $data->lastKnownPart);
        // set rest
        for ($i = $data->lastKnownPart + 1; $i <= $data->partsCount; $i++) {
            $dataPack = Support\Strings::substr($cont, $i * $data->lastKnownPart, $data->bytesPerPart, '');
            $this->processor->upload($data, $dataPack);
        }
        $this->processor->cancel($data); // intended, because pass will be checked in upload itself
    }

    /**
     * @throws Exceptions\UploadException
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
        $this->processor->init($pack);
        $this->processor->upload($pack, $cont);
        $data = $this->processor->done($pack);
        $this->assertEquals(8, $data->lastKnownPart);
        $this->assertEquals(8, $data->partsCount);
    }

    protected function mockKey(): ServerData\Data
    {
        $data = new ServerData\Data;
        $data->sharedKey = 'fghjkl' . Uploader\TargetSearch::FILE_DRIVER_SUFF;
        return $data;
    }

    protected function initProcessor(): void
    {
        $lang = new Uploader\Translations();
        $this->infoStorage = new Support\InfoRam($lang);
        $this->dataStorage = new Support\DataRam($lang);
        $key = new Support\ServerKey();
        $format = new InfoFormat\Json();
        $hashed = new Uploader\Hashed();
        $this->driveFile = new Uploader\DriveFile($this->infoStorage, $format, $key, $lang);
        $this->processor = new Uploader\Processor($this->driveFile, $this->dataStorage, $hashed, $lang);
    }
}
