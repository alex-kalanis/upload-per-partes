<?php

use UploadPerPartes\InfoFormat;

class CommonTestClass extends \PHPUnit\Framework\TestCase
{
//    public function providerBasic()
//    {
//        return Array(
//            0 => Array(new ORMTest()),
//            1 => Array(new ORMTestOld())
//        );
//    }

    protected function mockTestFile(): string
    {
        return $this->getTestDir() . 'testing.upload';
    }

    protected function mockSharedKey(): string
    {
        return 'driver.partial';
    }

    protected function getTestDir(): string
    {
        return realpath(__DIR__ . '/tmp/') . '/';
    }

    protected function getTestFile(): string
    {
        return realpath(__DIR__ . '/testing-ipsum.txt');
    }

    protected function mockData(): InfoFormat\Data
    {
        return InfoFormat\Data::init()->setData(
            'abcdef',
            $this->getTestDir() . 'abcdef',
            123456,
            12,
            64,
            7
        );
    }
}