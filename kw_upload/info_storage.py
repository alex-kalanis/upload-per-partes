from .uploader.translations import Translations
from .exceptions import UploadException
from .interfaces import IInfoStorage
import os


class Volume(IInfoStorage):
    """
     * Class Volume
     * Processing info file on disk volume
    """

    def __init__(self, lang: Translations):
        self._lang = lang

    def exists(self, key: str) -> bool:
        return os.path.isfile(key)

    def load(self, key: str) -> str:
        try:
            fp = open(key, 'r+')
            content = fp.read(10000)
            if not content:
                raise UploadException(self._lang.upp_drive_file_cannot_read(key))
            return str(content)

        except IsADirectoryError as err:
            raise UploadException(self._lang.upp_drive_file_cannot_read(key)) from err
        except PermissionError as err:
            raise UploadException(self._lang.upp_drive_file_cannot_read(key)) from err
        finally:
            if 'fp' in locals():
                fp.close()

    def save(self, key: str, data: str):
        try:
            fp = open(key, 'w')
            if 'fp' not in locals():
                raise UploadException(self._lang.upp_drive_file_cannot_write(key))
            if not fp.write(data):
                raise UploadException(self._lang.upp_drive_file_cannot_write(key))

        except IsADirectoryError as err:
            raise UploadException(self._lang.upp_drive_file_cannot_write(key)) from err
        except PermissionError as err:
            raise UploadException(self._lang.upp_drive_file_cannot_write(key)) from err
        finally:
            if 'fp' in locals():
                fp.close()

    def remove(self, key: str):
        try:
            os.unlink(key)
        except OSError as err:
            raise UploadException(self._lang.upp_drive_file_cannot_remove(key)) from err


class Redis(IInfoStorage):
    """
     * Class Redis
     * Processing info file on redis connection
    """
    import redis

    def __init__(self, lang: Translations, rc: redis.Redis):
        self._lang = lang
        self._rc = rc

    def exists(self, key: str) -> bool:
        return self._rc.exists(key)

    def load(self, key: str) -> str:
        return str(self._rc.get(key))

    def save(self, key: str, data: str):
        self._rc.set(key, data)

    def remove(self, key: str):
        self._rc.delete(key)


class MemCache(IInfoStorage):
    """
     * Class MemCache
     * Processing info file on Memcache connection
    """
    import pymemcache

    def __init__(self, lang: Translations, mc: pymemcache.Client):
        self._lang = lang
        self._mc = mc

    def exists(self, key: str) -> bool:
        return self._mc.get(key) is not None

    def load(self, key: str) -> str:
        return str(self._mc.get(key))

    def save(self, key: str, data: str):
        self._mc.set(key, data)

    def remove(self, key: str):
        self._mc.delete(key)
