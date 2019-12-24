import os
from .exceptions import UploadException, ContinuityUploadException
from .translations import Translations


class UploadData:

    def __init__(self):
        self.file_name = ''
        self.temp_name = ''
        self.file_size = 0
        self.parts_count = 0
        self.bytes_per_part = 0
        self.last_known_part = 0

    def set_data(self,
                 file_name: str,
                 temp_name: str,
                 file_size: int,
                 parts_count: int = 0,
                 bytes_per_part: int = 0,
                 last_known_part: int = 0
                 ):
        self.file_name = file_name
        self.temp_name = temp_name
        self.file_size = file_size
        self.parts_count = parts_count
        self.bytes_per_part = bytes_per_part
        self.last_known_part = last_known_part
        return self

    def sanitize_data(self):
        self.file_name = str(self.file_name)
        self.temp_name = str(self.temp_name)
        self.file_size = int(self.file_size)
        self.parts_count = int(self.parts_count)
        self.bytes_per_part = int(self.bytes_per_part)
        self.last_known_part = int(self.last_known_part)
        return self


class ADriveFile:
    """
     * Processing drive file - for each variant
    """

    VARIANT_TEXT = 1
    VARIANT_JSON = 2

    def __init__(self, lang: Translations, path: str):
        self._path = path
        self._lang = lang

    def exists(self) -> bool:
        return os.path.isfile(self._path)

    def load(self) -> UploadData:
        """
        :raise: UploadException
        :raise: NotImplementedError
        :return: Data
        """
        raise NotImplementedError('TBI')

    def save(self, data: UploadData):
        """
        :param data: Data
        :raise: UploadException
        :raise: NotImplementedError
        :return: void
        """
        raise NotImplementedError('TBI')

    def remove(self):
        if self.exists() and not os.unlink(path=self._path):
            raise UploadException(self._lang.drive_file_cannot_remove())
        return True

    @staticmethod
    def init(lang: Translations, variant: int, path: str):
        """

        :param lang: Translations
        :param variant: int
        :param path: str
        :return: ADriveFile
        :raise: UploadException
        """
        if ADriveFile.VARIANT_TEXT == variant:
            return Text(lang, path)
        elif ADriveFile.VARIANT_JSON == variant:
            return Json(lang, path)
        else:
            raise UploadException(lang.drive_file_variant_not_set())


class Text(ADriveFile):
    """
     * Processing driver file - variant plaintext
    """

    DATA_SEPARATOR = ':'
    LINE_SEPARATOR = "\r\n"

    def load(self) -> UploadData:
        handler = None
        try:
            handler = open(self._path)
            content = handler.readlines()
            handler.close()
        except IOError:
            if handler:
                handler.close()
            raise UploadException(self._lang.drive_file_cannot_read())
        if not content:
            raise UploadException(self._lang.drive_file_cannot_read())

        lib_data = UploadData()
        for line in content:
            if 0 < len(line):
                key, value, nothing = line.split(self.DATA_SEPARATOR, 3)
                setattr(lib_data, key, value)
        return lib_data.sanitize_data()

    def save(self, data: UploadData):
        data_keys = vars(data)
        data_lines = []
        for key in data_keys:
            data_lines.append(self.DATA_SEPARATOR.join([str(key), str(data_keys[key]), '']))
        handler = None
        try:
            handler = open(self._path, 'w')
            handler.write(self.LINE_SEPARATOR.join(data_lines))
            handler.close()
        except IOError:
            if handler:
                handler.close()
            raise UploadException(self._lang.drive_file_cannot_write())


class Json(ADriveFile):

    def load(self) -> UploadData:
        import json
        handler = None
        try:
            handler = open(self._path)
            content = handler.read(1000)
            handler.close()
        except IOError:
            if handler:
                handler.close()
            raise UploadException(self._lang.drive_file_cannot_read())
        if not content:
            raise UploadException(self._lang.drive_file_cannot_read())

        lib_data = UploadData()
        json_data = json.loads(content)
        for key in json_data.keys():
            setattr(lib_data, key, json_data[key])
        return lib_data.sanitize_data()

    def save(self, data: UploadData):
        import json
        handler = None
        try:
            handler = open(self._path, 'w')
            json.dump(vars(data), handler)
            handler.close()
        except IOError:
            if handler:
                handler.close()
            raise UploadException(self._lang.drive_file_cannot_write())


class DriveFile:
    """
     * Processing drive file
    """

    def __init__(self, lang: Translations, lib_driver: ADriveFile):
        self._lib_driver = lib_driver
        self._lang = lang

    def create(self, data: UploadData) -> bool:
        """
         * Create new drive file
        :param data: Data
        :return: bool
        :raise UploadException:
        :raise ContinuityUploadException:
        """
        if self._lib_driver.exists():
            raise ContinuityUploadException(self._lang.drive_file_already_exists())
        self._lib_driver.save(data)
        return True

    def read(self) -> UploadData:
        """
         * Read drive file
        :return: Data
        :raise UploadException:
        """
        return self._lib_driver.load()

    def update_last_part(self, last: int) -> bool:
        """
         * Update upload info
        :param last: int
        :return: bool
        :raise UploadException:
        """
        data = self._lib_driver.load()
        if (data.last_known_part + 1) != last:
            raise UploadException(self._lang.drive_file_not_continuous())
        data.last_known_part = last
        self._lib_driver.save(data)
        return True

    def remove(self) -> bool:
        """
         * Delete drive file - usually on finish or discard
        :return: bool
        :raise UploadException:
        """
        self._lib_driver.remove()
        return True
