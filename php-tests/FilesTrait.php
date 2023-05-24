<?php

use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\UploadPerPartes\Interfaces\IDataStorage;
use kalanis\UploadPerPartes\Uploader\Translations;


class FilesTrait
{
    // just for loading this file
    public function mockFilesDataPass(): IDataStorage
    {
        return new \kalanis\UploadPerPartes\DataStorage\Files(new CompositeAdapter(
            new NodePass(),
            new DirPass(),
            new FilePass()
        ), new Translations());
    }

    public function mockFilesDataDie(): IDataStorage
    {
        return new \kalanis\UploadPerPartes\DataStorage\Files(new CompositeAdapter(
            new \NodeFail(),
            new \DirFail(),
            new \FileFail()
        ), new Translations());
    }

    public function mockFilesInfoPass(): \kalanis\UploadPerPartes\Interfaces\IInfoStorage
    {
        return new \kalanis\UploadPerPartes\InfoStorage\Files(new CompositeAdapter(
            new NodePass(),
            new DirPass(),
            new FilePass()
        ), new Translations());
    }

    public function mockFilesInfoDie(): \kalanis\UploadPerPartes\Interfaces\IInfoStorage
    {
        return new \kalanis\UploadPerPartes\InfoStorage\Files(new CompositeAdapter(
            new \NodeFail(),
            new \DirFail(),
            new \FileFail()
        ), new Translations());
    }
}


trait TNameToStr
{
    protected function toStr(array $name): string
    {
        return implode('--', $name);
    }
}


class NodePass implements \kalanis\kw_files\Interfaces\IProcessNodes
{
    use TNameToStr;

    public function exists(array $entry): bool
    {
        return true;
    }

    public function isReadable(array $entry): bool
    {
        return true;
    }

    public function isWritable(array $entry): bool
    {
        return true;
    }

    public function isDir(array $entry): bool
    {
        return false;
    }

    public function isFile(array $entry): bool
    {
        return true;
    }

    public function size(array $entry): ?int
    {
        return null;
    }

    public function created(array $entry): ?int
    {
        return null;
    }
}


class FilePass implements \kalanis\kw_files\Interfaces\IProcessFiles
{
    use TNameToStr;

    protected $data = [];

    public function saveFile(array $entry, $content, ?int $offset = null): bool
    {
        if (is_null($offset)) {
            $this->data[$this->toStr($entry)] = $content;
        } else {
            $data = $this->readFile($entry);
            $this->data[$this->toStr($entry)] = substr($data, 0, $offset) . $content;
        }
        return true;
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        $data = isset($this->data[$this->toStr($entry)]) ? $this->data[$this->toStr($entry)] : '';
        if (!is_null($offset)) {
            if (is_null($length)) {
                return substr($data, $offset);
            } else {
                return substr($data, $offset, $length);
            }
        } else {
            if (!is_null($length)) {
                return substr($data, 0, $length);
            } else {
                return $data;
            }
        }
    }

    public function copyFile(array $source, array $dest): bool
    {
        $this->data[$this->toStr($dest)] = $this->data[$this->toStr($source)];
        return true;
    }

    public function moveFile(array $source, array $dest): bool
    {
        $this->copyFile($source, $dest);
        $this->deleteFile($source);
        return true;
    }

    public function deleteFile(array $entry): bool
    {
        unset($this->data[$this->toStr($entry)]);
        return true;
    }
}


class DirPass implements \kalanis\kw_files\Interfaces\IProcessDirs
{
    use TNameToStr;

    public function createDir(array $entry, bool $deep = false): bool
    {
        return true;
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        return [];
    }

    public function copyDir(array $source, array $dest): bool
    {
        return true;
    }

    public function moveDir(array $source, array $dest): bool
    {
        return true;
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        return true;
    }
}


class NodeFail implements \kalanis\kw_files\Interfaces\IProcessNodes
{
    public function exists(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function isReadable(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function isWritable(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function isDir(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function isFile(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function size(array $entry): ?int
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function created(array $entry): ?int
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }
}


class FileFail implements \kalanis\kw_files\Interfaces\IProcessFiles
{
    public function saveFile(array $entry, $content, ?int $offset = null): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function copyFile(array $source, array $dest): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function moveFile(array $source, array $dest): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function deleteFile(array $entry): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }
}


class DirFail implements \kalanis\kw_files\Interfaces\IProcessDirs
{
    public function createDir(array $entry, bool $deep = false): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function copyDir(array $source, array $dest): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function moveDir(array $source, array $dest): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }
}
