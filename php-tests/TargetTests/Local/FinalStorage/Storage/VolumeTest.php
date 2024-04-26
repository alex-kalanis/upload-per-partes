<?php

namespace TargetTests\Local\FinalStorage\Storage;


use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Target\Local\FinalStorage;


class VolumeTest extends AStorageTest
{
    public function tearDown(): void
    {
        parent::tearDown();
        $f2 = $this->getTestDir() . 'testing__0.upload';
        if (is_file($f2)) {
            chmod($f2, 0555);
            unlink($f2);
        }
        $f3 = $this->getTestDir() . 'testing__1.upload';
        if (is_file($f3)) {
            chmod($f3, 0555);
            unlink($f3);
        }
        $f4 = $this->getTestDir() . 'testing__2.upload';
        if (is_file($f4)) {
            chmod($f4, 0555);
            unlink($f4);
        }
    }

    protected function getLib(): Interfaces\IFinalStorage
    {
        return new FinalStorage\Storage\Volume($this->getTestDir());
    }
}
