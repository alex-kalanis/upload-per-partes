from kw_tests.common_class import CommonTestClass
from kw_tests.support import Files
from kw_upload.info_storage import Volume
from kw_upload.info_storage import AStorage as InfoStorage
from kw_upload.exceptions import UploadException
from kw_upload.uploader.translations import Translations


class AInfoStorage(CommonTestClass):

    def tearDown(self):
        if Files.is_file(self._mock_test_file()):
            self._mock_storage().remove(self._mock_test_file())
        super().tearDown()

    def _mock_storage(self) -> InfoStorage:
        return Volume(Translations())

    def test_thru(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        assert not storage.exists(file)
        storage.save(file, 'abcdefghijklmnopqrstuvwxyz')
        assert storage.exists(file)
        storage.load(file)
        storage.remove(file)
        assert not storage.exists(file)

    def test_unreadable(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.save(file, 'abcdefghijklmnopqrstuvwxyz')
        storage.load(file)
        Files.chmod(file, 0o333)
        try:
            storage.load(file)  # fail
            assert False, 'Accessing unreadable!'
        except UploadException as ex:
            assert 'CANNOT READ DRIVEFILE' == ex.get_message()

    def test_unwriteable(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.save(file, 'abcdefghijklmnopqrstuvwxyz')
        Files.chmod(file, 0o444)
        try:
            storage.save(file, 'abcdefghijklmnopqrstuvwxyz')  # fail
            assert False, 'Accessing unreadable!'
        except UploadException as ex:
            assert 'CANNOT WRITE DRIVEFILE' == ex.get_message()

    def test_deleted(self):
        file = self._mock_test_file()
        storage = self._mock_storage()
        storage.save(file, 'abcdefghijklmnopqrstuvwxyz')
        try:
            storage.remove(file)
            storage.remove(file)  # dies here
            assert False, 'Deleting non-existent file!'
        except UploadException as ex:
            assert 'DRIVEFILE CANNOT BE REMOVED' == ex.get_message()
