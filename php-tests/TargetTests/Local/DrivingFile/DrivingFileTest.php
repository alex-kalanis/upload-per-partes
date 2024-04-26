<?php

namespace TargetTests\Local\DrivingFile;


use CommonTestClass;
use kalanis\kw_storage\Storage;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Uploader\Config;
use kalanis\UploadPerPartes\UploadException;


class DrivingFileTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThruServer(): void
    {
        $factory = new Local\DrivingFile\Factory();
        $conf = new Config([
            'driving_file' => new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()),
            'key_encoder' => Local\DrivingFile\KeyEncoders\Name::class,
            'key_modifier' => Local\DrivingFile\KeyModifiers\Hex::class,
            'data_encoder' => Local\DrivingFile\DataEncoders\Serialize::class,
            'data_modifier' => Local\DrivingFile\DataModifiers\Clear::class,
        ]);
        $mock = $this->mockData();
        $lib = $factory->getDrivingFile($conf);
        $this->assertFalse($lib->existsByData($mock));
        $what = $lib->storeByData($this->mockData());
        $this->assertNotEmpty($what);
        $data = $lib->get($what);
        $this->assertNotEmpty($data);
        $this->assertEquals($mock->tempDir, $data->tempDir);
        $this->assertEquals($mock->tempName, $data->tempName);
        $this->assertEquals($mock->targetDir, $data->targetDir);
        $this->assertEquals($mock->targetName, $data->targetName);
        $this->assertEquals($mock->fileSize, $data->fileSize);
        $this->assertEquals($mock->partsCount, $data->partsCount);
        $this->assertEquals($mock->bytesPerPart, $data->bytesPerPart);
        $this->assertEquals($mock->lastKnownPart, $data->lastKnownPart);
        $this->assertTrue($lib->removeByData($this->mockData()));
    }

    /**
     * @throws UploadException
     */
    public function testThruClient(): void
    {
        $factory = new Local\DrivingFile\Factory();
        $conf = new Config([
            'driving_file' => 'client',
            'key_encoder' => Local\DrivingFile\KeyEncoders\Serialize::class, // client - will be ignored
            'key_modifier' => Local\DrivingFile\KeyModifiers\Clear::class, // client - will be ignored
            'data_encoder' => Local\DrivingFile\DataEncoders\Serialize::class,
            'data_modifier' => Local\DrivingFile\DataModifiers\Clear::class,
        ]);
        $mock = $this->mockData();
        $lib = $factory->getDrivingFile($conf);
        $this->assertTrue($lib->existsByData($mock));
        $what = $lib->storeByData($this->mockData());
        $this->assertNotEmpty($what);
        $data = $lib->get($what);
        $this->assertNotEmpty($data);
        $this->assertEquals($mock->tempDir, $data->tempDir);
        $this->assertEquals($mock->tempName, $data->tempName);
        $this->assertEquals($mock->targetDir, $data->targetDir);
        $this->assertEquals($mock->targetName, $data->targetName);
        $this->assertEquals($mock->fileSize, $data->fileSize);
        $this->assertEquals($mock->partsCount, $data->partsCount);
        $this->assertEquals($mock->bytesPerPart, $data->bytesPerPart);
        $this->assertEquals($mock->lastKnownPart, $data->lastKnownPart);
        $this->assertTrue($lib->removeByData($this->mockData()));
    }
}
