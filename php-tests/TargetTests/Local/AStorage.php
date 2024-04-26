<?php

namespace TargetTests\Local;


use CommonTestClass;


abstract class AStorage extends CommonTestClass
{
    public function setUp(): void
    {
        if (is_file($this->mockTestFile())) {
            chmod($this->mockTestFile(), 0555);
            unlink($this->mockTestFile());
        }
        if (is_dir($this->mockTestFile())) {
            rmdir($this->mockTestFile());
        }
        parent::setUp();
    }

    public function tearDown(): void
    {
        if (is_file($this->mockTestFile())) {
            chmod($this->mockTestFile(), 0555);
            unlink($this->mockTestFile());
        }
        if (is_dir($this->mockTestFile())) {
            rmdir($this->mockTestFile());
        }
        parent::tearDown();
    }
}
