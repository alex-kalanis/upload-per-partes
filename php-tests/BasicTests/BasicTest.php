<?php

namespace BasicTests;


use CommonTestClass;
use Support;
use kalanis\UploadPerPartes\Uploader\Calculates;


class BasicTest extends CommonTestClass
{
    /**
     * beware, i need to test this, because it's necessary for run - it happens for me to got failed testing
     * PHP has problems with badly defined params, it cost me 3hrs
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testStrings(): void
    {
        $this->assertEquals('bcdef',  substr('abcdef', 1));
        $this->assertEquals('bcd',    substr('abcdef', 1, 3));
        $this->assertEquals('abcd',   substr('abcdef', 0, 4));
        $this->assertEquals('abcdef', substr('abcdef', 0, 8));
        $this->assertEquals('f',      substr('abcdef', -1, 1));

        // now with lib
        $this->assertEquals('bcdef',  Support\Strings::substr('abcdef', 1, null, ''));
        $this->assertEquals('bcd',    Support\Strings::substr('abcdef', 1, 3, ''));
        $this->assertEquals('abcd',   Support\Strings::substr('abcdef', 0, 4, ''));
        $this->assertEquals('abcdef', Support\Strings::substr('abcdef', 0, 8, ''));
        $this->assertEquals('f',      Support\Strings::substr('abcdef', -1, 1, ''));
    }

    /**
     * @throws \kalanis\UploadPerPartes\Exceptions\UploadException
     */
    public function testCalculate(): void
    {
        $lib = Calculates::init();
        $this->assertEquals(Calculates::DEFAULT_BYTES_PER_PART, $lib->getBytesPerPart());

        $lib2 = Calculates::init(20);
        $this->assertEquals(20, $lib2->getBytesPerPart());
        $this->assertEquals(2, $lib2->calcParts(35));
        $this->assertEquals(2, $lib2->calcParts(40));
        $this->assertEquals(3, $lib2->calcParts(41));
    }
}
