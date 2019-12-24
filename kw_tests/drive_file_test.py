import os
import pytest
from .common_class import CommonTestClass
from kw_upload.drive_file import ADriveFile, DriveFile, Text, Json
from kw_upload.translations import Translations
from kw_upload.exceptions import UploadException


class DriveFilesTest (CommonTestClass):

    def tearDown(self):
        if os.path.isfile(self._mock_test_file()):
            os.unlink(path=self._mock_test_file())
        super().tearDown()

    def test_data_file(self):
        data = self._mock_data()
        assert 'abcdef' == data.file_name
        assert 'abcdef' == data.temp_name
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_text(self):
        lang = Translations()
        lib1 = Text(lang, self._mock_test_file())
        lib1.save(self._mock_data())

        lib2 = ADriveFile.init(lang, ADriveFile.VARIANT_TEXT, self._mock_test_file())
        data = lib2.load()

        assert 'abcdef' == data.file_name
        assert 'abcdef' == data.temp_name
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_json(self):
        lang = Translations()
        lib1 = Json(lang, self._mock_test_file())
        lib1.save(self._mock_data())

        lib2 = ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file())
        data = lib2.load()

        assert 'abcdef' == data.file_name
        assert 'abcdef' == data.temp_name
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_drive_file(self):
        lang = Translations()
        lib1 = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))
        lib1.create(self._mock_data())

        lib2 = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))
        data = lib2.read()

        assert 'abcdef' == data.file_name
        assert 'abcdef' == data.temp_name
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_drive_file_exists(self):
        lang = Translations()
        with pytest.raises(UploadException):
            lib = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))

            lib1 = Text(lang, self._mock_test_file())
            lib1.save(self._mock_data())

            lib.create(self._mock_data())

    def test_drive_file_updater(self):
        lang = Translations()
        lib = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))
        lib.create(self._mock_data())
        assert lib.update_last_part(8) is True

    def test_drive_file_updater_hole(self):
        lang = Translations()
        with pytest.raises(UploadException):
            lib = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))
            lib.create(self._mock_data())
            lib.update_last_part(10)
            lib.remove()
