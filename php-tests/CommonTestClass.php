<?php

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
        return $this->getTestDir() . 'testing.partial';
    }

    protected function mockSharedKey(): string
    {
        return 'driver.partial';
    }

    protected function getTestDir(): string
    {
//        return realpath(__DIR__ . '/tmp/') . '/';
        return realpath('/tmp/') . '/';
    }

    protected function getTestFile(): string
    {
        return realpath(__DIR__ . '/testing-ipsum.txt');
    }
}