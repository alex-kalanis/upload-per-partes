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

    def add_part(self, location: str, content: bytes, seek: int = None):
        """
         * Add part to file
        :param location: string
        :param content:
        :param seek:
        :return void:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')

    def get_part(self, location: str, offset: int, limit: int = None) -> bytes:
        """
         * Get part of file
        :param location:
        :param offset:
        :param limit:
        :return void:
        :raise UploadException:
        raise NotImplementedError('TBA')
        """

    def truncate(self, location: str, offset: int):
        """
         * Truncate data file
        :param location:
        :param offset:
        :return void:
        :raise UploadException:
        """

    def remove(self, location: str):
        """
         * Remove whole data file
        :param location:
        :return void:
        :raise UploadException:
        """


class VolumeBasic(AStorage):
    """
     * Class VolumeBasic
     * Processing info file on disk volume
     * Filesystem behaves oddly - beware of fucked up caching!
    """

    def add_part(self, location: str, content: bytes, seek: int = None):
        if not seek:  # append to end
            try:
                fp = open(location, 'ab')
            except IsADirectoryError:
                raise UploadException(self._lang.cannot_open_file())
            except PermissionError:
                raise UploadException(self._lang.cannot_write_file())
            if not fp:
                raise UploadException(self._lang.cannot_open_file())
            if not fp.write(content):
                fp.close()
                raise UploadException(self._lang.cannot_write_file())
            fp.close()
        else:  # append from position
            try:
                fp = open(location, 'rb+')
            except IsADirectoryError:
                raise UploadException(self._lang.cannot_open_file())
            except PermissionError:
                raise UploadException(self._lang.cannot_write_file())
            if not fp:
                raise UploadException(self._lang.cannot_open_file())
            if not fp.seek(seek):
                fp.close()
                raise UploadException(self._lang.cannot_seek_file())
            if not fp.write(content):
                fp.close()
                raise UploadException(self._lang.cannot_write_file())
            fp.close()

    def get_part(self, location: str, offset: int, limit: int = None) -> bytes:
        try:
            fp = open(location, 'rb')
        except IsADirectoryError:
            raise UploadException(self._lang.cannot_open_file())
        except PermissionError:
            raise UploadException(self._lang.cannot_read_file())

        if not fp:
            raise UploadException(self._lang.cannot_open_file())

        if not limit:
            fp.seek(0, 2)
            limit = fp.tell()

        position = fp.seek(offset, 0)
        if position < 0:
            fp.close()
            raise UploadException(self._lang.cannot_seek_file())

        data = fp.read(limit if limit else -1)
        if not data:
            fp.close()
            raise UploadException(self._lang.cannot_read_file())

        fp.close()
        return bytes(data)

    def truncate(self, location: str, offset: int):
        try:
            fp = open(location, 'rb+')
        except IsADirectoryError:
            raise UploadException(self._lang.cannot_open_file())
        except PermissionError:
            raise UploadException(self._lang.cannot_truncate_file())

        fp.seek(0)
        if not fp.truncate(offset):
            fp.close()
            raise UploadException(self._lang.cannot_truncate_file())
        fp.seek(0)
        fp.close()

    def remove(self, location: str):
        try:
            os.unlink(location)
        except OSError:
            raise UploadException(self._lang.cannot_remove_data())
