<?php

use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\UploadPerPartes\Interfaces;
use kalanis\UploadPerPartes\Uploader\Translations;


class FilesTrait
{
    // just for loading this file
    public function mockFilesDataPass(): Interfaces\IDataStorage
    {
        return new \kalanis\UploadPerPartes\DataStorage\Files(new CompositeAdapter(
            new NodePass(),
            new DirPass(),
            new FilePass(),
            new StreamPass()
        ), [], new Translations());
    }

    public function mockFilesDataDie(): Interfaces\IDataStorage
    {
        return new \kalanis\UploadPerPartes\DataStorage\Files(new CompositeAdapter(
            new NodeFail(),
            new DirFail(),
            new FileFail(),
            new StreamFail()
        ), [], new Translations());
    }

    public function mockFilesInfoPass(): Interfaces\IInfoStorage
    {
        return new \kalanis\UploadPerPartes\ServerData\InfoStorage\Files(new CompositeAdapter(
            new NodePass(),
            new DirPass(),
            new FilePass(),
            new StreamPass()
        ), [], new Translations());
    }

    public function mockFilesInfoDie(): Interfaces\IInfoStorage
    {
        return new \kalanis\UploadPerPartes\ServerData\InfoStorage\Files(new CompositeAdapter(
            new NodeFail(),
            new DirFail(),
            new FileFail(),
            new StreamFail()
        ), [], new Translations());
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

    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        if (is_null($offset)) {
            $this->data[$this->toStr($entry)] = $content;
        } else {
            $data = $this->readFile($entry);
            $this->data[$this->toStr($entry)] = substr($data, 0, $offset) . $content;
        }
        return true;
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
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


class StreamPass implements \kalanis\kw_files\Interfaces\IProcessFileStreams
{
    use TNameToStr;

    protected $data = [];

    public function saveFileStream(array $entry, $content, int $mode = 0): bool
    {
        $this->data[$this->toStr($entry)] = $content;
        return true;
    }

    public function readFileStream(array $entry)
    {
        $key = $this->toStr($entry);
        if (!isset($this->data[$key])) {
            throw new \kalanis\kw_files\FilesException('Not exists');
        }
        return $this->data[$key];
    }

    public function copyFileStream(array $source, array $dest): bool
    {
        return $this->saveFileStream($dest, $this->readFileStream($source));
    }

    public function moveFileStream(array $source, array $dest): bool
    {
        $v1 = $this->copyFileStream($source, $dest);
        $v2 = $this->deleteFileStream($source);
        return $v1 && $v2;
    }

    public function deleteFileStream(array $entry): bool
    {
        $key = $this->toStr($entry);
        if (isset($this->data[$key])) {
            @fclose($this->data[$key]);
            unset($this->data[$key]);
        }
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
    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
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


class StreamFail implements \kalanis\kw_files\Interfaces\IProcessFileStreams
{
    public function saveFileStream(array $entry, $content, int $mode = 0): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function readFileStream(array $entry)
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function copyFileStream(array $source, array $dest): bool
    {
        throw new \kalanis\kw_files\FilesException('mock');
    }

    public function moveFileStream(array $source, array $dest): bool
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
