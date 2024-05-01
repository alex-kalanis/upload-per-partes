<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local;
use kalanis\UploadPerPartes\Uploader;


class ConfigTest extends CommonTestClass
{
    public function testSizes1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(0, $lib->bytesPerPart);
    }

    public function testSizes2(): void
    {
        $lib = new Uploader\Config(['calc_size' => 333]);
        $this->assertEquals(333, $lib->bytesPerPart);
    }

    public function testSizes3(): void
    {
        $lib = new Uploader\Config(['calc_size' => -66]);
        $this->assertEquals(1, $lib->bytesPerPart);
    }

    public function testSizes4(): void
    {
        $lib = new Uploader\Config(['calc_size' => 'wtf']);
        $this->assertEquals(1, $lib->bytesPerPart);
    }

    public function testSizes5(): void
    {
        $lib = new Uploader\Config(['calc_size' => false]);
        $this->assertEquals(1, $lib->bytesPerPart);
    }

    public function testTempLocation1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals('', $lib->tempDir);
    }

    public function testTempLocation2(): void
    {
        $lib = new Uploader\Config(['temp_location' => 'baz']);
        $this->assertEquals('baz', $lib->tempDir);
    }

    public function testTempLocation3(): void
    {
        $lib = new Uploader\Config(['temp_location' => -66]);
        $this->assertEquals('-66', $lib->tempDir);
    }

    public function testTempLocation4(): void
    {
        $lib = new Uploader\Config(['temp_location' => new class {
            public function __toString()
            {
                return 'wwhh';
            }
        }]);
        $this->assertEquals('wwhh', $lib->tempDir);
    }

    public function testTargetLocation1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals('', $lib->targetDir);
    }

    public function testTargetLocation2(): void
    {
        $lib = new Uploader\Config(['target_location' => 'baz']);
        $this->assertEquals('baz', $lib->targetDir);
    }

    public function testTargetLocation3(): void
    {
        $lib = new Uploader\Config(['target_location' => -66]);
        $this->assertEquals('-66', $lib->targetDir);
    }

    public function testTargetLocation4(): void
    {
        $lib = new Uploader\Config(['target_location' => new class {
            public function __toString()
            {
                return 'wwhh';
            }
        }]);
        $this->assertEquals('wwhh', $lib->targetDir);
    }

    public function testLang1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->lang);
    }

    public function testLang2(): void
    {
        $lib = new Uploader\Config(['lang' => new \XTrans()]);
        $this->assertInstanceOf(Interfaces\IUppTranslations::class, $lib->lang);
    }

    public function testLang3(): void
    {
        $lib = new Uploader\Config(['lang' => -66]);
        $this->assertEquals('-66', $lib->lang);
    }

    public function testLang4(): void
    {
        $lib = new Uploader\Config(['lang' => new \stdClass()]);
        $this->assertEquals(null, $lib->lang);
    }

    public function testTarget1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->target);
    }

    public function testTarget2(): void
    {
        $lib = new Uploader\Config(['target' => new \XFailOper()]);
        $this->assertInstanceOf(Interfaces\IOperations::class, $lib->target);
    }

    public function testTarget3(): void
    {
        $lib = new Uploader\Config(['target' => -66]);
        $this->assertEquals('-66', $lib->target);
    }

    public function testTarget4(): void
    {
        $lib = new Uploader\Config(['target' => new \stdClass()]);
        $this->assertEquals(null, $lib->target);
    }

    public function testDataEncoder1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->dataEncoder);
    }

    public function testDataEncoder2(): void
    {
        $lib = new Uploader\Config(['data_encoder' => new \XFailDrivingDataEncoder()]);
        $this->assertInstanceOf(Local\DrivingFile\DataEncoders\AEncoder::class, $lib->dataEncoder);
    }

    public function testDataEncoder3(): void
    {
        $lib = new Uploader\Config(['data_encoder' => -66]);
        $this->assertEquals('-66', $lib->dataEncoder);
    }

    public function testDataEncoder4(): void
    {
        $lib = new Uploader\Config(['data_encoder' => new \stdClass()]);
        $this->assertEquals(null, $lib->dataEncoder);
    }

    public function testDataModifier1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->dataModifier);
    }

    public function testDataModifier2(): void
    {
        $lib = new Uploader\Config(['data_modifier' => new \XFailDrivingDataModifier()]);
        $this->assertInstanceOf(Local\DrivingFile\DataModifiers\AModifier::class, $lib->dataModifier);
    }

    public function testDataModifier3(): void
    {
        $lib = new Uploader\Config(['data_modifier' => -66]);
        $this->assertEquals('-66', $lib->dataModifier);
    }

    public function testDataModifier4(): void
    {
        $lib = new Uploader\Config(['data_modifier' => new \stdClass()]);
        $this->assertEquals(null, $lib->dataModifier);
    }

    public function testKeyEncoder1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->keyEncoder);
    }

    public function testKeyEncoder2(): void
    {
        $lib = new Uploader\Config(['key_encoder' => new \XFailDrivingkeyEncoder()]);
        $this->assertInstanceOf(Local\DrivingFile\keyEncoders\AEncoder::class, $lib->keyEncoder);
    }

    public function testKeyEncoder3(): void
    {
        $lib = new Uploader\Config(['key_encoder' => -66]);
        $this->assertEquals('-66', $lib->keyEncoder);
    }

    public function testKeyEncoder4(): void
    {
        $lib = new Uploader\Config(['key_encoder' => new \stdClass()]);
        $this->assertEquals(null, $lib->keyEncoder);
    }

    public function testKeyModifier1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->keyModifier);
    }

    public function testKeyModifier2(): void
    {
        $lib = new Uploader\Config(['key_modifier' => new \XFailDrivingkeyModifier()]);
        $this->assertInstanceOf(Local\DrivingFile\keyModifiers\AModifier::class, $lib->keyModifier);
    }

    public function testKeyModifier3(): void
    {
        $lib = new Uploader\Config(['key_modifier' => -66]);
        $this->assertEquals('-66', $lib->keyModifier);
    }

    public function testKeyModifier4(): void
    {
        $lib = new Uploader\Config(['key_modifier' => new \stdClass()]);
        $this->assertEquals(null, $lib->keyModifier);
    }

    public function testDrivingStore1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->drivingFileStorage);
    }

    public function testDrivingStore2(): void
    {
        $lib = new Uploader\Config(['driving_file' => new \stdClass()]);
        $this->assertInstanceOf(\stdClass::class, $lib->drivingFileStorage);
    }

    public function testDrivingStore3(): void
    {
        $lib = new Uploader\Config(['driving_file' => -66]);
        $this->assertEquals(-66, $lib->drivingFileStorage);
    }

    public function testTempStore1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->temporaryStorage);
    }

    public function testTempStore2(): void
    {
        $lib = new Uploader\Config(['temp_storage' => new \stdClass()]);
        $this->assertInstanceOf(\stdClass::class, $lib->temporaryStorage);
    }

    public function testTempStore3(): void
    {
        $lib = new Uploader\Config(['temp_storage' => -66]);
        $this->assertEquals(-66, $lib->temporaryStorage);
    }

    public function testTempModifier1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->temporaryEncoder);
    }

    public function testTempModifier2(): void
    {
        $lib = new Uploader\Config(['temp_encoder' => new \XFailTempEncoder()]);
        $this->assertInstanceOf(Local\TemporaryStorage\KeyEncoders\AEncoder::class, $lib->temporaryEncoder);
    }

    public function testTempModifier3(): void
    {
        $lib = new Uploader\Config(['temp_encoder' => -66]);
        $this->assertEquals('-66', $lib->temporaryEncoder);
    }

    public function testTempModifier4(): void
    {
        $lib = new Uploader\Config(['temp_encoder' => new \stdClass()]);
        $this->assertEquals(null, $lib->temporaryEncoder);
    }

    public function testFinalStore1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->finalStorage);
    }

    public function testFinalStore2(): void
    {
        $lib = new Uploader\Config(['final_storage' => new \stdClass()]);
        $this->assertInstanceOf(\stdClass::class, $lib->finalStorage);
    }

    public function testFinalStore3(): void
    {
        $lib = new Uploader\Config(['final_storage' => -66]);
        $this->assertEquals(-66, $lib->finalStorage);
    }

    public function testFinalModifier1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->finalEncoder);
    }

    public function testFinalModifier2(): void
    {
        $lib = new Uploader\Config(['final_encoder' => new \XFailFinalEncoder()]);
        $this->assertInstanceOf(Local\FinalStorage\KeyEncoders\AEncoder::class, $lib->finalEncoder);
    }

    public function testFinalModifier3(): void
    {
        $lib = new Uploader\Config(['final_encoder' => -66]);
        $this->assertEquals('-66', $lib->finalEncoder);
    }

    public function testFinalModifier4(): void
    {
        $lib = new Uploader\Config(['final_encoder' => new \stdClass()]);
        $this->assertEquals(null, $lib->finalEncoder);
    }

    public function testChecksum1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->checksum);
    }

    public function testChecksum2(): void
    {
        $lib = new Uploader\Config(['checksum' => \XFailChecksum::class]);
        $this->assertEquals(\XFailChecksum::class, $lib->checksum);
    }

    public function testChecksum3(): void
    {
        $lib = new Uploader\Config(['checksum' => -66]);
        $this->assertEquals('-66', $lib->checksum);
    }

    public function testChecksum4(): void
    {
        $lib = new Uploader\Config(['checksum' => \stdClass::class]);
        $this->assertEquals(\stdClass::class, $lib->checksum);
    }

    public function testDecoder1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertEquals(null, $lib->decoder);
    }

    public function testDecoder2(): void
    {
        $lib = new Uploader\Config(['decoder' => \XFailDecoder::class]);
        $this->assertEquals(\XFailDecoder::class, $lib->decoder);
    }

    public function testDecoder3(): void
    {
        $lib = new Uploader\Config(['decoder' => -66]);
        $this->assertEquals('-66', $lib->decoder);
    }

    public function testDecoder4(): void
    {
        $lib = new Uploader\Config(['decoder' => \stdClass::class]);
        $this->assertEquals(\stdClass::class, $lib->decoder);
    }

    public function testContinue1(): void
    {
        $lib = new Uploader\Config([]);
        $this->assertTrue($lib->canContinue);
    }

    public function testContinue2(): void
    {
        $lib = new Uploader\Config(['can_continue' => 66]);
        $this->assertTrue($lib->canContinue);
    }

    public function testContinue3(): void
    {
        $lib = new Uploader\Config(['can_continue' => false]);
        $this->assertFalse($lib->canContinue);
    }
}
