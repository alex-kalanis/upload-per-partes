from kw_tests.common_class import CommonTestClass
from kw_tests.support import Files, Dirs, DataRam, InfoRam
from kw_upload.data_storage import VolumeBasic
from kw_upload.data_storage import AStorage as DataStorage
from kw_upload.uploader.essentials import Calculates, Hashed, TargetSearch
from kw_upload.exceptions import UploadException
from kw_upload.uploader.translations import Translations


class ADataStorageTest(CommonTestClass):

    def tearDown(self):
        if Files.is_file(self._mock_test_file()):
            Files.unlink(self._mock_test_file())
        if Dirs.is_dir(self._mock_test_file()):
            Dirs.rmdir(self._mock_test_file())
        super().tearDown()

    def _mock_storage(self) -> DataStorage:
        return VolumeBasic(Translations())


class VolumeTest(ADataStorageTest):

    def test_thru(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz')
        assert b'abcdefghijklmnopqrstuvwxyz' == storage.get_part(file, 0)
        storage.truncate(file, 16)
        assert b'abcdefghijklmnop' == storage.get_part(file, 0)
        storage.remove(file)
        assert not Files.is_file(file)

    def test_unreadable(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        Dirs.mkdir(file)
        try:
            storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz')  # fail
            assert False, 'Accessing unreadable!'
        except UploadException as ex:
            assert 'CANNOT OPEN FILE' == ex.get_message()
        finally:
            Dirs.rmdir(file)

    def test_unreadable_seek(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        Dirs.mkdir(file)
        try:
            storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz', 10)  # fail
            assert False, 'Accessing unreadable!'
        except UploadException as ex:
            assert 'CANNOT OPEN FILE' == ex.get_message()
        finally:
            Dirs.rmdir(file)

    def test_unwriteable(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz')
        Files.chmod(file, 0o444)
        try:
            storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz')  # fail
            assert False, 'Writing to locked file!'
        except UploadException as ex:
            assert 'CANNOT WRITE FILE' == ex.get_message()
        finally:
            Files.chmod(file, 0o666)
            storage.remove(self._mock_test_file())

    def test_unwriteable_seek(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz', 0)
        Files.chmod(file, 0o444)
        try:
            storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz', 26)  # fail
            assert False, 'Writing to non-available seek in file!'
        except UploadException as ex:
            assert 'CANNOT WRITE FILE' == ex.get_message()
        finally:
            Files.chmod(file, 0o666)
            storage.remove(self._mock_test_file())

    def test_deleted(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        assert not storage.exists(file)
        storage.add_part(file, b'abcdefghijklmnopqrstuvwxyz', 0)
        assert storage.exists(file)
        try:
            storage.remove(file)
            storage.remove(file)  # fail
            assert False, 'Deleting non-existent file!'
        except UploadException as ex:
            assert 'CANNOT REMOVE DATA' == ex.get_message()


class TargetTest(CommonTestClass):

    def test_fail_no_remote(self):
        lang = Translations()
        lib = TargetSearch(lang, InfoRam(lang), DataRam(lang))
        try:
            lib.process()
            assert False, 'No remote and passed'
        except UploadException as ex:
            assert 'SENT FILE NAME IS EMPTY' == ex.get_message()

    def test_fail_no_target(self):
        lang = Translations()
        lib = TargetSearch(lang, InfoRam(lang), DataRam(lang))
        lib.set_remote_file_name('abcdefg')
        try:
            lib.process()
            assert False, 'No target and passed'
        except UploadException as ex:
            assert 'TARGET DIR IS NOT SET' == ex.get_message()

    def test_fail_no_base(self):
        lang = Translations()
        lib = TargetSearch(lang, InfoRam(lang), DataRam(lang))
        try:
            lib.get_final_target_name()
            assert False, 'No final target name and passed'
        except UploadException as ex:
            assert 'UPLOAD FILE NAME IS EMPTY' == ex.get_message()

    def test_process_clear(self):
        lang = Translations()
        lib = TargetSearch(lang, InfoRam(lang), DataRam(lang))
        lib.set_target_dir(self._get_test_dir()).set_remote_file_name('what can be found$.here').process()
        assert 'what_can_be_found.here' == lib.get_final_target_name()
        assert self._get_test_dir() + 'what_can_be_found' + TargetSearch.FILE_DRIVER_SUFF == lib.get_driver_location()
        assert self._get_test_dir() + 'what_can_be_found.here' + TargetSearch.FILE_UPLOAD_SUFF == lib.get_temporary_target_location()

    def test_process_no_clear(self):
        lang = Translations()
        lib = TargetSearch(lang, InfoRam(lang), DataRam(lang), False, False)
        lib.set_target_dir(self._get_test_dir()).set_remote_file_name('what el$e can be found').process()
        assert 'what el$e can be found' == lib.get_final_target_name()
        assert self._get_test_dir() + 'what el$e can be found' + TargetSearch.FILE_DRIVER_SUFF == lib.get_driver_location()
        assert self._get_test_dir() + 'what el$e can be found' + TargetSearch.FILE_UPLOAD_SUFF == lib.get_temporary_target_location()

    def test_process_name_lookup(self):
        lang = Translations()
        data_ram = DataRam(lang)
        data_ram.add_part(self._get_test_dir() + 'dummyFile.tst', 'asdfghjklqwertzuiopyxcvbnm')
        data_ram.add_part(self._get_test_dir() + 'dummyFile.0.tst', 'asdfghjklqwertzuiopyxcvbnm')
        data_ram.add_part(self._get_test_dir() + 'dummyFile.1.tst', 'asdfghjklqwertzuiopyxcvbnm')
        data_ram.add_part(self._get_test_dir() + 'dummyFile.2.tst', 'asdfghjklqwertzuiopyxcvbnm')
        lib = TargetSearch(lang, InfoRam(lang), data_ram, False, False)
        lib.set_target_dir(self._get_test_dir()).set_remote_file_name('dummyFile.tst').process()
        assert self._get_test_dir() + 'dummyFile.3.tst' + TargetSearch.FILE_UPLOAD_SUFF == lib.get_temporary_target_location()
