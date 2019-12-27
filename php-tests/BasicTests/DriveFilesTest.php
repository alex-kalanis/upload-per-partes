<?php

use UploadPerPartes\DriveFile;
use UploadPerPartes\Translations;

class DriveFilesTest extends CommonTestClass
{
    public function tearDown()
    {
        if (is_file($this->mockTestFile())) {
            $lib = new DriveFile\Text(Translations::init(), $this->mockTestFile());
            $lib->remove();
        }
        parent::tearDown();
    }

    public function testDataFile()
    {
        $data = $this->mockData();
        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
    }

    public function testProcessTextSimple()
    {
        $lib = new DriveFile\Text(Translations::init(), $this->mockTestFile());
        $this->assertEmpty($lib->save($this->mockData()));
        $this->assertTrue($lib->exists());
        $data = $lib->load();

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $this->assertTrue($lib->remove());
    }

    public function testProcessJsonSimple()
    {
        $lib = new DriveFile\Json(Translations::init(), $this->mockTestFile());
        $this->assertEmpty($lib->save($this->mockData()));
        $this->assertTrue($lib->exists());
        $data = $lib->load();

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $this->assertTrue($lib->remove());
    }

    public function testProcessTextDynamic()
    {
        $lib1 = new DriveFile\Text(Translations::init(), $this->mockTestFile());
        $lib1->save($this->mockData());

        $lib2 = DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_TEXT, $this->mockTestFile());
        $data = $lib2->load();

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $lib2->remove();
    }

    public function testProcessJsonDynamic()
    {
        $lib1 = new DriveFile\Json(Translations::init(), $this->mockTestFile());
        $lib1->save($this->mockData());

        $lib2 = DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile());
        $data = $lib2->load();

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $lib2->remove();
    }

    public function testDriveFile()
    {
        $lib1 = new DriveFile(Translations::init(), DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile()));
        $lib1->create($this->mockData());

        $lib2 = new DriveFile(Translations::init(), DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile()));
        $data = $lib2->read();

        $this->assertEquals('abcdef', $data->fileName);
        $this->assertEquals($this->getTestDir() . 'abcdef', $data->tempPath);
        $this->assertEquals(123456, $data->fileSize);
        $this->assertEquals(12, $data->partsCount);
        $this->assertEquals(64, $data->bytesPerPart);
        $this->assertEquals(7, $data->lastKnownPart);
        $lib2->remove();
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     */
    public function testDriveFileExists()
    {
        $lib = new DriveFile(Translations::init(), DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile()));

        $lib1 = new DriveFile\Text(Translations::init(), $this->mockTestFile());
        $lib1->save($this->mockData());

        $lib->create($this->mockData());
        $lib1->remove();
    }

    public function testDriveFileUpdater()
    {
        $lib = new DriveFile(Translations::init(), DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile()));
        $lib->create($this->mockData());
        $this->assertTrue($lib->updateLastPart($this->mockData(), 8));
        $lib->remove();
    }

    /**
     * @expectedException  \UploadPerPartes\Exceptions\UploadException
     */
    public function testDriveFileUpdaterHole()
    {
        $lib = new DriveFile(Translations::init(), DriveFile\ADriveFile::init(Translations::init(), DriveFile\ADriveFile::VARIANT_JSON, $this->mockTestFile()));
        $lib->create($this->mockData());
        $lib->updateLastPart($this->mockData(), 10);
        $lib->remove();
    }
}