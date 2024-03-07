<?php

namespace ServerDataTests\InfoStorage;


use CommonTestClass;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\Storage\Key\DefaultKey;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\ServerData;


class StorageTest extends CommonTestClass
{
    /**
     * @throws UploadException
     */
    public function testThru(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorage();
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
        $this->assertTrue($storage->exists($file));
        $storage->load($file);
        $storage->remove($file);
        $this->assertFalse($storage->exists($file));
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageFail();
        $this->expectExceptionMessage('CANNOT READ DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->load($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnreadable2(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageCrash();
        $this->expectExceptionMessage('CANNOT READ DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->load($file);
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageFail();
        $this->expectExceptionMessage('CANNOT WRITE DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @throws UploadException
     */
    public function testUnwrittable2(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageCrash();
        $this->expectExceptionMessage('CANNOT WRITE DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->save($file, 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * @throws UploadException
     */
    public function testDeleted(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageFail();
        $this->expectExceptionMessage('DRIVEFILE CANNOT BE REMOVED');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testDeleted2(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageCrash();
        $this->expectExceptionMessage('DRIVEFILE CANNOT BE REMOVED');
        $this->expectException(UploadException::class);
        $storage->remove($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testExistsDied(): void
    {
        $file = $this->mockTestFile();
        $storage = $this->mockStorageCrashExist();
        $this->expectExceptionMessage('CANNOT READ DRIVEFILE');
        $this->expectException(UploadException::class);
        $storage->exists($file); // dies here
    }

    /**
     * @throws UploadException
     */
    public function testClassPass(): void
    {
        $this->assertTrue($this->mockStorage()->checkKeyClasses(
            new XStorageModifyPass(),
            new XStorageEncodePass(),
            new XStorageFormatPass()
        ));
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyModifier(): void
    {
        $this->expectExceptionMessage('KEY MODIFIER IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XStorageModifyFail(),
            new XStorageEncodePass(),
            new XStorageFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailStorageKeyVariant(): void
    {
        $this->expectExceptionMessage('KEY VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XStorageModifyPass(),
            new XStorageEncodeFail(),
            new XStorageFormatPass()
        );
    }

    /**
     * @throws UploadException
     */
    public function testClassFailDataFormat(): void
    {
        $this->expectExceptionMessage('DRIVEFILE VARIANT IS WRONG');
        $this->expectException(UploadException::class);
        $this->mockStorage()->checkKeyClasses(
            new XStorageModifyPass(),
            new XStorageEncodePass(),
            new XStorageFormatFail()
        );
    }

    protected function mockStorage(): Interfaces\IInfoStorage
    {
        return new ServerData\InfoStorage\Storage(new Storage\Storage(new DefaultKey(), new XRemStorage()));
    }

    protected function mockStorageFail(): Interfaces\IInfoStorage
    {
        return new ServerData\InfoStorage\Storage(new Storage\Storage(new DefaultKey(), new XFailStorage()));
    }

    protected function mockStorageCrash(): Interfaces\IInfoStorage
    {
        return new ServerData\InfoStorage\Storage(new Storage\Storage(new DefaultKey(), new XCrashStorage()));
    }

    protected function mockStorageCrashExist(): Interfaces\IInfoStorage
    {
        return new ServerData\InfoStorage\Storage(new Storage\Storage(new DefaultKey(), new XCrashExistenceStorage()));
    }
}


class XRemStorage implements ITarget
{
    protected $data = [];

    public function check(string $key): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function load(string $key)
    {
        return $this->exists($key) ? $this->data[$key] : null ;
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        $this->data[$key] = $data;
        return true;
    }

    public function remove(string $key): bool
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }
        return true;
    }

    public function lookup(string $key): \Traversable
    {
        yield from $this->data;
    }

    public function increment(string $key): bool
    {
        $this->save($key, $this->exists($key) ? $this->load($key) + 1 : 1);
        return true;
    }

    public function decrement(string $key): bool
    {
        $this->save($key, $this->exists($key) ? $this->load($key) - 1 : 0);
        return true;
    }

    public function removeMulti(array $keys): array
    {
        return [];
    }
}


class XFailStorage implements ITarget
{
    protected $data = [];

    public function check(string $key): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return true;
    }

    public function load(string $key)
    {
        throw new StorageException('not load');
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        return false;
    }

    public function remove(string $key): bool
    {
        return false;
    }

    public function lookup(string $key): \Traversable
    {
        yield from [];
    }

    public function increment(string $key): bool
    {
        return false;
    }

    public function decrement(string $key): bool
    {
        return false;
    }

    public function removeMulti(array $keys): array
    {
        return [];
    }
}


class XCrashStorage implements ITarget
{
    protected $data = [];

    public function check(string $key): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return true;
    }

    public function load(string $key)
    {
        throw new StorageException('not load');
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        throw new StorageException('not save');
    }

    public function remove(string $key): bool
    {
        throw new StorageException('not del');
    }

    public function lookup(string $key): \Traversable
    {
        throw new StorageException('not look');
    }

    public function increment(string $key): bool
    {
        throw new StorageException('not incr');
    }

    public function decrement(string $key): bool
    {
        throw new StorageException('not decr');
    }

    public function removeMulti(array $keys): array
    {
        return [];
    }
}


class XCrashExistenceStorage extends XCrashStorage
{
    public function exists(string $key): bool
    {
        throw new StorageException('not available');
    }
}


class XStoreInfoFormatting implements Interfaces\IInfoFormatting
{
    public function fromFormat(string $content): ServerData\Data
    {
        return new ServerData\Data();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XStorageModifyFail implements Interfaces\ILimitPassedData
{
    public function getLimitedData(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XStorageModifyPass extends XStorageModifyFail implements Interfaces\InfoStorage\ForStorage
{}


class XStorageEncodeFail implements Interfaces\IEncodeSharedKey
{
    public function pack(string $data): string
    {
        return 'mock';
    }

    public function unpack(string $data): string
    {
        return 'mock';
    }
}


class XStorageEncodePass extends XStorageEncodeFail implements Interfaces\InfoStorage\ForStorage
{}


class XStorageFormatFail implements Interfaces\IInfoFormatting
{
    public function fromFormat(string $content): ServerData\Data
    {
        return new ServerData\Data();
    }

    public function toFormat(ServerData\Data $data): string
    {
        return 'mock';
    }
}


class XStorageFormatPass extends XStorageFormatFail implements Interfaces\InfoStorage\ForStorage
{}
