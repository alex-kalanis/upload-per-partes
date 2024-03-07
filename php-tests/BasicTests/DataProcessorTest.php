<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Exceptions;
use kalanis\UploadPerPartes\ServerData;
use kalanis\UploadPerPartes\Uploader;
use Support;


class DataProcessorTest extends CommonTestClass
{
    /**
     * @throws Exceptions\UploadException
     */
    public function testThru(): void
    {
        $processor = $this->getDataProcessor();
        $data = $processor->upload($this->mockData(), '1234567890abcdefghijklmnopqrstuvwxyz');
        $this->assertInstanceOf(ServerData\Data::class, $data);
        $this->assertEquals('--test--', $data->remoteName);
        $this->assertEquals('', $data->tempDir);
        $this->assertEquals('fghjkl' . ServerData\TargetFileName::FILE_DRIVER_SUFF, $data->tempName);
        $this->assertEquals(0, $data->fileSize);
        $this->assertEquals(38, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(12, $data->lastKnownPart);
        $this->assertEquals('928f7bcdcd08869cc44c1bf24e7abec6', $processor->check($this->mockData(), 0));
        $this->assertTrue($processor->cancel($this->mockData()));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentAdd(): void
    {
        $this->assertNotEmpty($this->getDataProcessor()->upload($this->mockData(), 'this_segment'));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentAddPosition(): void
    {
        $this->assertNotEmpty($this->getDataProcessor()->upload($this->mockData(), 'this_segment', 12));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentAddPositionFail(): void
    {
        $this->expectExceptionMessage('READ TOO EARLY');
        $this->expectException(Exceptions\UploadException::class);
        $this->getDataProcessor()->upload($this->mockData(), 'this_segment', 13);
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testTruncate(): void
    {
        $this->assertNotEmpty($this->getDataProcessor()->truncateFrom($this->mockData(), 6));
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testTruncateFail(): void
    {
        $this->expectExceptionMessage('DRIVEFILE IS NOT CONTINUOUS');
        $this->expectException(Exceptions\UploadException::class);
        $this->getDataProcessor()->xUpdateLastPart($this->mockData(), 13); // fail
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentSubZero(): void
    {
        $this->expectExceptionMessage('SEGMENT OUT OF BOUNDS');
        $this->expectException(Exceptions\UploadException::class);
        $this->getDataProcessor()->check($this->mockData(), -1); // fail
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentOutsideCounter(): void
    {
        $this->expectExceptionMessage('SEGMENT OUT OF BOUNDS');
        $this->expectException(Exceptions\UploadException::class);
        $this->getDataProcessor()->check($this->mockData(), 40); // fail
    }

    /**
     * @throws Exceptions\UploadException
     */
    public function testSegmentOutsideKnown(): void
    {
        $this->expectExceptionMessage('SEGMENT NOT UPLOADED YET');
        $this->expectException(Exceptions\UploadException::class);
        $this->getDataProcessor()->check($this->mockData(), 12); // fail
    }

    protected function mockData(): ServerData\Data
    {
        $data = new ServerData\Data();
        $data->tempDir = '';
        $data->tempName = 'fghjkl' . ServerData\TargetFileName::FILE_DRIVER_SUFF;
        $data->remoteName = '--test--';
        $data->bytesPerPart = 64;
        $data->partsCount = 38;
        $data->lastKnownPart = 11;
        return $data;
    }

    protected function getDataProcessor(): XDataProcessor
    {
        $lang = new Uploader\Translations();
        return new XDataProcessor(
            new Support\DataRam($lang),
            new Uploader\CheckByHash(),
            $lang
        );
    }
}


class XDataProcessor extends Uploader\DataProcessor
{
    /**
     * @param ServerData\Data $data
     * @param int $last
     * @param bool $checkContinuous
     * @throws Exceptions\UploadException
     * @return ServerData\Data
     */
    public function xUpdateLastPart(ServerData\Data $data, int $last, bool $checkContinuous = true): ServerData\Data
    {
        return parent::updateLastPart($data, $last, $checkContinuous);
    }
}
