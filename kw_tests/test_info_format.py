from kw_tests.common_class import CommonTestClass
from kw_tests.support import Files
from kw_upload.info_format import Factory, AFormat, Json, Text
from kw_upload.info_storage import Volume
from kw_upload.exceptions import UploadException
from kw_upload.uploader.translations import Translations


class InfoFormatTest(CommonTestClass):

    def tearDown(self):
        if Files.is_file(self._mock_test_file()):
            lib = Volume(Translations())
            lib.remove(self._mock_test_file())
        super().tearDown()

    def test_init(self):
        lang = Translations()
        assert isinstance(Factory.get_format(lang, Factory.FORMAT_TEXT), Text)
        assert isinstance(Factory.get_format(lang, Factory.FORMAT_JSON), Json)

    def test_init_fail(self):
        try:
            Factory.get_format(Translations(), 0)  # fail
            assert False, 'Accessing unreadable!'
        except UploadException as ex:
            assert 'DRIVEFILE VARIANT NOT SET' == ex.get_message()

    def test_text(self):
        lib = Text()
        target = lib.to_format(self._mock_data())
        data = lib.from_format(target)

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_location
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_json(self):
        lib = Json()
        target = lib.to_format(self._mock_data())
        data = lib.from_format(target)

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_location
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part
