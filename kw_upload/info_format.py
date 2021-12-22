from .uploader.translations import Translations
from .exceptions import UploadException


class DataPack:

    @staticmethod
    def init():
        return DataPack()

    def __init__(self):
        self.file_name = ''
        self.temp_location = ''
        self.file_size = 0
        self.parts_count = 0
        self.bytes_per_part = 0
        self.last_known_part = 0

    def set_data(self,
                 file_name: str,
                 temp_location: str,
                 file_size: int,
                 parts_count: int = 0,
                 bytes_per_part: int = 0,
                 last_known_part: int = 0
                 ):
        self.file_name = file_name
        self.temp_location = temp_location
        self.file_size = file_size
        self.parts_count = parts_count
        self.bytes_per_part = bytes_per_part
        self.last_known_part = last_known_part
        return self

    def sanitize_data(self):
        self.file_name = str(self.file_name)
        self.temp_location = str(self.temp_location)
        self.file_size = int(self.file_size)
        self.parts_count = int(self.parts_count)
        self.bytes_per_part = int(self.bytes_per_part)
        self.last_known_part = int(self.last_known_part)
        return self


class AFormat:
    """
     * Class AFormat
     * Drive file format - abstract for each variant
    """

    def from_format(self, content: str) -> DataPack:
        """
        :param content:
        :return DataPack:
        :raise UploadException:
        """
        raise NotImplementedError('TBI')

    def to_format(self, data: DataPack) -> str:
        """
        :param data:
        :return str:
        :raise UploadException:
        """
        raise NotImplementedError('TBI')


class Text(AFormat):
    """
     * Processing driver file - variant plaintext
    """

    DATA_SEPARATOR = ':'
    LINE_SEPARATOR = "\r\n"

    def from_format(self, content: str) -> DataPack:
        lib_data = DataPack()
        for line in content.split(self.LINE_SEPARATOR):
            if 0 < len(line):
                key, value, nothing = line.split(self.DATA_SEPARATOR, 3)
                setattr(lib_data, key, value)
        return lib_data.sanitize_data()

    def to_format(self, data: DataPack) -> str:
        data_keys = vars(data)
        data_lines = []
        for key in data_keys:
            data_lines.append(self.DATA_SEPARATOR.join([str(key), str(data_keys[key]), '']))
        return self.LINE_SEPARATOR.join(data_lines)


class Json(AFormat):

    def from_format(self, content: str) -> DataPack:
        import json
        lib_data = DataPack()
        json_data = json.loads(content)
        for key in json_data.keys():
            setattr(lib_data, key, json_data[key])
        return lib_data.sanitize_data()

    def to_format(self, data: DataPack) -> str:
        import json
        return json.dumps(vars(data))


class Factory:
    """
     * Class Factory
     * Drive file format - Factory to get formats
    """

    FORMAT_TEXT = 1
    FORMAT_JSON = 2

    @staticmethod
    def get_format(lang: Translations, variant: int):
        """
        :param lang: Translations
        :param variant: int
        :return: AFormat
        :raise: UploadException
        """
        if Factory.FORMAT_TEXT == variant:
            return Text()
        elif Factory.FORMAT_JSON == variant:
            return Json()
        else:
            raise UploadException(lang.drive_file_variant_not_set())
