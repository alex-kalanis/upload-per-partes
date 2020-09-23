from kw_upload.data_storage import AStorage as DataStorage
from kw_upload.exceptions import UploadException
from kw_upload.keys import AKey
from kw_upload.info_storage import AStorage as InfoStorage
from kw_upload.uploader.translations import Translations


class Strings:
    """
     * Processing Strings - reimplementation of necessary methods (contains fuckups)
     * PHP uses relative positioning, Python absolute one
    """
    @staticmethod
    def substr(what: str, offset: int = 0, limit: int = None) -> str:
        """
         * Original one returns shitty results, so need re-implement parts
         * It's necessary to have nullable limit, not only set as undefined
        :param what:
        :param offset:
        :param limit:
        :param error_message:
        :return: str
        :raise: UploadException
        """
        length = len(what)
        if limit and (abs(limit) > length):  # not over
            limit = None
        if not limit:
            result = what[offset:]
        else:
            # make it absolute from begin
            offset = offset if offset == abs(offset) else length + offset
            limit = offset + limit if limit == abs(limit) else length + limit
            (offset, limit) = (offset, limit) if offset <= limit else (limit, offset)  # typically pythonic
            result = what[offset:limit]
        return result


class Files:

    @staticmethod
    def file_get_contents(filename: str, size: int = 10000, encode: str = 'utf-8') -> str:
        f = open(filename, 'r', encoding=encode)
        cont = f.read(size)
        f.close()
        return str(cont)

    @staticmethod
    def file_put_contents(filename: str, content: str):
        f = open(filename, 'w')
        f.write(content)
        f.close()

    @staticmethod
    def is_file(filename: str) -> bool:
        import os
        return os.path.isfile(filename)

    @staticmethod
    def unlink(filename: str):
        import os
        os.unlink(filename)

    @staticmethod
    def chmod(filename: str, mode: int):
        import os
        os.chmod(filename, mode)

    @staticmethod
    def realpath(filepath: str):
        import os
        os.path.realpath(filepath)


class Dirs:

    @staticmethod
    def mkdir(dirname: str):
        import os
        return os.mkdir(dirname)

    @staticmethod
    def is_dir(dirname: str) -> bool:
        import os
        return os.path.isdir(dirname)

    @staticmethod
    def rmdir(dirname: str):
        import os
        os.rmdir(dirname)


class DataRam(DataStorage):
    """
     * Processing data file on ram volume
    """

    def __init__(self, lang: Translations):
        super().__init__(lang)
        self._data = b''

    def add_part(self, location: str, content: bytes, seek: int = None):
        self._data = (self._data if seek is None else self._data[0:seek]) + content

    def get_part(self, location: str, offset: int, limit: int = None) -> bytes:
        return self._data[offset:] if limit is None else self._data[offset:offset+limit]

    def truncate(self, location: str, offset: int):
        self._data = self._data[0:offset]

    def remove(self, location: str):
        self._data = ''

    def get_all(self, location: str = '') -> bytes:
        return self.get_part(location, 0, None)


class InfoRam(InfoStorage):
    """
     * Processing info file on ram volume
    """

    def __init__(self, lang: Translations):
        super().__init__(lang)
        self._data = ''

    def exists(self, key: str) -> bool:
        return bool(len(self._data))

    def load(self, key: str) -> str:
        content = self._data
        if not bool(len(content)):
            raise UploadException(self._lang.drive_file_cannot_read())
        return content

    def save(self, key: str, data: str):
        self._data = data

    def remove(self, key: str):
        self._data = ''


class Key(AKey):

    def from_shared_key(self, key: str) -> str:
        return 'php://memory'

    def generate_keys(self):
        self._shared_key = self._target.get_final_target_name() + self._target.FILE_DRIVER_SUFF
        return self
