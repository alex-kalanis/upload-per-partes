import hashlib
import re
from .translations import Translations
from kw_upload.exceptions import UploadException
from kw_upload.data_storage import AStorage as DataStorage
from kw_upload.info_storage import AStorage as InfoStorage


class Calculates:
    """
     * Class Calculates
     * Calculations over sizes
    """

    DEFAULT_BYTES_PER_PART = 262144  # 1024*256

    @staticmethod
    def init(bytes_per_part: int = None):
        return Calculates(bytes_per_part)

    def __init__(self, bytes_per_part: int = None):
        self._bytes_per_part = bytes_per_part if bytes_per_part else self.DEFAULT_BYTES_PER_PART

    def get_bytes_per_part(self) -> int:
        return self._bytes_per_part

    def calc_parts(self, length: int) -> int:
        parts_counter = int(length / self._bytes_per_part)
        return parts_counter if ((length % self._bytes_per_part) == 0) else (parts_counter + 1)


class Hashed:
    """
     * Class Hashed
     * Calculations hashes, need for checking content
     * Basic one is MD5
    """
    def calc_hash(self, content: bytes) -> str:
        return hashlib.md5(str(content.decode()).encode()).hexdigest()  # do not ask why...


class TargetSearch:
    """
     * Class TargetSearch
     * Search possible target path
    """

    FILE_DRIVER_SUFF = '.partial'
    FILE_UPLOAD_SUFF = '.upload'
    FILE_EXT_SEP = '.'
    FILE_VER_SEP = '.'
    WIN_NAME_LEN_LIMIT = 110  # minus dot, len upload and part for multiple file upload - win allows max 128 chars, rest is for path

    def __init__(self, lang: Translations, info_storage: InfoStorage, data_storage: DataStorage, sanitize_whitespace: bool = True, sanitize_alnum: bool = True):
        self._lang = lang
        self._info_storage = info_storage
        self._data_storage = data_storage
        self._sanitize_whitespace = sanitize_whitespace
        self._sanitize_alnum = sanitize_alnum

        self._remote_file_name = ''
        self._target_dir = ''

        self._file_base = ''
        self._file_suff = ''

    def set_target_dir(self, target_dir: str):
        self._target_dir = target_dir
        return self

    def set_remote_file_name(self, file_name: str):
        self._remote_file_name = file_name
        return self

    def process(self):
        self._check_remote_name()
        self._check_target_dir()
        self._canonize()
        if not self._info_storage.exists(self.get_driver_location()):
            self._find_free_name()
        return self

    def get_driver_location(self) -> str:
        self._check_file_base()
        return self._target_dir + self._file_base + self.FILE_DRIVER_SUFF

    def get_final_target_name(self) -> str:
        self._check_file_base()
        return self._file_base + self._add_ext()

    def get_temporary_target_location(self) -> str:
        self._check_file_base()
        return self._target_dir + self._file_base + self._add_ext() + self.FILE_UPLOAD_SUFF

    def _check_remote_name(self):
        if not len(self._remote_file_name):
            raise UploadException(self._lang.sent_name_is_empty())

    def _check_target_dir(self):
        if not len(self._target_dir):
            raise UploadException(self._lang.target_dir_is_empty())

    def _check_file_base(self):
        if not len(self._file_base):
            raise UploadException(self._lang.upload_name_is_empty())

    def _find_free_name(self):
        """
         * Find non-existing name
        """
        ext = self._add_ext()
        if self._data_storage.exists(self._target_dir + self._file_base + ext):
            i = 0
            while True:
                location = self._file_base + self.FILE_VER_SEP + str(i)
                i = i+1
                if not self._data_storage.exists(self._target_dir + location + ext):
                    break
            self._file_base = location

    def _canonize(self):
        f = re.sub(r'((&[A-Za-z]{1,6};)|(&#[A-Za-z0-9]{1,7};))', '', self._remote_file_name)
        if self._sanitize_alnum:
            f = re.sub(r'[^A-Za-z0-9\s\-\.]', '', f)  # remove all which is not alnum or dots
        if self._sanitize_whitespace:
            f = re.sub(r'[\s]', '_', f)  # whitespaces to underscore

        self._file_suff = self._file_ext(f)
        max_len = self.WIN_NAME_LEN_LIMIT - len(self._file_suff)  # win limit... cut more due possibility of uploading multiple files with same name
        self._file_base = self._file_name(f)[0:max_len]

    def _file_ext(self, file_name: str) -> str:
        pos = file_name.rfind(self.FILE_EXT_SEP)
        return '' if 1 > pos else file_name[pos+1:]

    def _file_name(self, file_name: str) -> str:
        pos = file_name.rfind(self.FILE_EXT_SEP)
        if -1 == pos:
            return file_name
        if 0 < pos:
            return file_name[0:pos]
        return file_name[1:]

    def _add_ext(self) -> str:
        return self.FILE_EXT_SEP + self._file_suff if self._has_ext() else ''

    def _has_ext(self) -> bool:
        return 0 < len(self._file_suff)
