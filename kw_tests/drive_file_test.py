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
        assert self._get_test_dir() + 'abcdef' == data.temp_path
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_text_simple(self):
        lib = Text(Translations(), self._mock_test_file())
        assert lib.save(self._mock_data()) is None
        assert lib.exists() is True
        data = lib.load()

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_path
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_json_simple(self):
        lib = Json(Translations(), self._mock_test_file())
        assert lib.save(self._mock_data()) is None
        assert lib.exists() is True
        data = lib.load()

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_path
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_text_dynamic(self):
        lang = Translations()
        lib1 = Text(lang, self._mock_test_file())
        lib1.save(self._mock_data())

        lib2 = ADriveFile.init(lang, ADriveFile.VARIANT_TEXT, self._mock_test_file())
        data = lib2.load()

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_path
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part

    def test_process_json_dynamic(self):
        lang = Translations()
        lib1 = Json(lang, self._mock_test_file())
        lib1.save(self._mock_data())

        lib2 = ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file())
        data = lib2.load()

        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_path
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
        assert self._get_test_dir() + 'abcdef' == data.temp_path
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
        assert lib.update_last_part(self._mock_data(), 8) is True

    def test_drive_file_updater_hole(self):
        lang = Translations()
        with pytest.raises(UploadException):
            lib = DriveFile(lang, ADriveFile.init(lang, ADriveFile.VARIANT_JSON, self._mock_test_file()))
            lib.create(self._mock_data())
            lib.update_last_part(self._mock_data(), 10)
            lib.remove()
