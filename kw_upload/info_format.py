from .interfaces import IInfoFormatting
from .uploader.data import DataPack
from .uploader.translations import Translations
from .exceptions import UploadException


class Text(IInfoFormatting):
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


class Json(IInfoFormatting):

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
    def get_format(lang: Translations, variant: int) -> IInfoFormatting:
        """
        :param lang: Translations
        :param variant: int
        :return: IInfoFormatting
        :raise: UploadException
        """
        if Factory.FORMAT_TEXT == variant:
            return Text()
        elif Factory.FORMAT_JSON == variant:
            return Json()
        else:
            raise UploadException(lang.upp_drive_file_variant_not_set())
