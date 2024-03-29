from .uploader.translations import Translations
from .interfaces import IDataStorage
from .exceptions import UploadException


class VolumeBasic(IDataStorage):
    """
     * Class VolumeBasic
     * Processing info file on disk volume
     * Filesystem behaves oddly - beware of fucked up caching!
    """
    def __init__(self, lang: Translations):
        self._lang = lang

    def exists(self, location: str) -> bool:
        import os.path
        return os.path.exists(location)

    def add_part(self, location: str, content: bytes, seek: int = None):
        if not seek:  # append to end
            try:
                fp = open(location, 'ab')
                if 'fp' not in locals():
                    raise UploadException(self._lang.upp_cannot_open_file(location))
                if not fp.write(content):
                    raise UploadException(self._lang.upp_cannot_write_file(location))
            except IsADirectoryError as err:
                raise UploadException(self._lang.upp_cannot_open_file(location)) from err
            except PermissionError as err:
                raise UploadException(self._lang.upp_cannot_write_file(location)) from err
            finally:
                if 'fp' in locals():
                    fp.close()
        else:  # append from position
            try:
                fp = open(location, 'rb+')
                if 'fp' not in locals():
                    raise UploadException(self._lang.upp_cannot_open_file(location))
                if not fp.seek(seek):
                    raise UploadException(self._lang.upp_cannot_seek_file(location))
                if not fp.write(content):
                    raise UploadException(self._lang.upp_cannot_write_file(location))
            except IsADirectoryError as err:
                raise UploadException(self._lang.upp_cannot_open_file(location)) from err
            except PermissionError as err:
                raise UploadException(self._lang.upp_cannot_write_file(location)) from err
            finally:
                if 'fp' in locals():
                    fp.close()

    def get_part(self, location: str, offset: int, limit: int = None) -> bytes:
        try:
            fp = open(location, 'rb')
            if 'fp' not in locals():
                raise UploadException(self._lang.upp_cannot_open_file(location))

            if not limit:
                fp.seek(0, 2)
                limit = fp.tell()

            position = fp.seek(offset, 0)
            if position < 0:
                raise UploadException(self._lang.upp_cannot_seek_file(location))

            data = fp.read(limit if limit else -1)
            if not data:
                raise UploadException(self._lang.upp_cannot_read_file(location))

            return bytes(data)

        except IsADirectoryError as err:
            raise UploadException(self._lang.upp_cannot_open_file()) from err
        except PermissionError as err:
            raise UploadException(self._lang.upp_cannot_read_file()) from err
        finally:
            if 'fp' in locals():
                fp.close()

    def truncate(self, location: str, offset: int):
        try:
            fp = open(location, 'rb+')
            if 'fp' not in locals():
                raise UploadException(self._lang.upp_cannot_open_file(location))
            fp.seek(0)
            if not fp.truncate(offset):
                raise UploadException(self._lang.upp_cannot_truncate_file(location))
            fp.seek(0)
        except IsADirectoryError as err:
            raise UploadException(self._lang.upp_cannot_open_file(location)) from err
        except PermissionError as err:
            raise UploadException(self._lang.upp_cannot_truncate_file(location)) from err
        finally:
            if 'fp' in locals():
                fp.close()

    def remove(self, location: str):
        import os
        try:
            os.unlink(location)
        except OSError as err:
            raise UploadException(self._lang.upp_cannot_remove_data(location)) from err
