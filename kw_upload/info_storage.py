from .uploader.translations import Translations
from .exceptions import UploadException
import os


class AStorage:
    """
     * Class AStorage
     * Target storage for data stream
    """

    def __init__(self, lang: Translations):
        self._lang = lang

    def exists(self, key: str) -> bool:
        raise NotImplementedError('TBI')

    def load(self, key: str) -> str:
        raise NotImplementedError('TBI')

    def save(self, key: str, data: str):
        raise NotImplementedError('TBI')

    def remove(self, key: str):
        raise NotImplementedError('TBI')


class Volume(AStorage):
    """
     * Class Volume
     * Processing info file on disk volume
    """

    def exists(self, key: str) -> bool:
        return os.path.isfile(key)

    def load(self, key: str) -> str:
        try:
            fp = open(key, 'r+')
        except IsADirectoryError:
            raise UploadException(self._lang.drive_file_cannot_read())
        except PermissionError:
            raise UploadException(self._lang.drive_file_cannot_read())

        content = fp.read(10000)
        if not content:
            raise UploadException(self._lang.drive_file_cannot_read())
        fp.close()
        return str(content)

    def save(self, key: str, data: str):
        try:
            fp = open(key, 'w')
        except IsADirectoryError:
            raise UploadException(self._lang.drive_file_cannot_write())
        except PermissionError:
            raise UploadException(self._lang.drive_file_cannot_write())
        if not fp:
            raise UploadException(self._lang.drive_file_cannot_write())
        if not fp.write(data):
            fp.close()
            raise UploadException(self._lang.drive_file_cannot_write())
        fp.close()

    def remove(self, key: str):
        try:
            os.unlink(key)
        except OSError:
            raise UploadException(self._lang.drive_file_cannot_remove())


class Redis(AStorage):
    """
     * Class Redis
     * Processing info file on redis connection
    """
    import redis

    def __init__(self, lang: Translations, rc: redis.Redis):
        super().__init__(lang)
        self._rc = rc

    def exists(self, key: str) -> bool:
        return self._rc.exists(key)

    def load(self, key: str) -> str:
        return str(self._rc.get(key))

    def save(self, key: str, data: str):
        self._rc.set(key, data)

    def remove(self, key: str):
        self._rc.delete(key)


class MemCache(AStorage):
    """
     * Class MemCache
     * Processing info file on Memcache connection
    """
    import pymemcache

    def __init__(self, lang: Translations, mc: pymemcache.Client):
        super().__init__(lang)
        self._mc = mc

    def exists(self, key: str) -> bool:
        return self._mc.get(key) is not None

    def load(self, key: str) -> str:
        return str(self._mc.get(key))

    def save(self, key: str, data: str):
        self._mc.set(key, data)

    def remove(self, key: str):
        self._mc.delete(key)
