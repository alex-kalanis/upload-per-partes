import os
import filecmp
from .common_class import CommonTestClass
from kw_upload.responses import AResponse, InitResponse, UploadResponse, DoneResponse
from kw_upload.upload import Upload


class UploadMock(Upload):
    def __init__(self, target_path: str, shared_key: str = None):
        super().__init__(target_path, shared_key)
        self._bytes_per_part = 512


class UploadTest(CommonTestClass):

    def tearDown(self):
        if os.path.isfile(self._mock_test_file()):
            os.unlink(path=self._mock_test_file())
        super().tearDown()

    def test_simple_upload(self):
        # step 1 - init driver
        lib1 = UploadMock(self._get_test_dir())
        max_size = os.path.getsize(self._get_test_file())
        result1 = lib1.partes_init('lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']

        # step 2 - send data
        lib2 = UploadMock(self._get_test_dir(), result1.get_result()['sharedKey'])
        try:
            i = 0
            result2 = self._read_file(lib2, i)
            while i * lib2._bytes_per_part < max_size:
                i += 1
                result2 = self._read_file(lib2, i)
            assert UploadResponse.STATUS_OK == result2.get_result()['status']
        except IOError as ex:
            assert False, 'There is some problem with files'

        # step 3 - close upload
        lib3 = UploadMock(self._get_test_dir(), result1.get_result()['sharedKey'])
        result3 = lib3.partes_done()

        # check content
        assert DoneResponse.STATUS_OK == result3.get_result()['status']
        assert filecmp.cmp(result3.get_target_file(), self._get_test_file()) is True

        if os.path.isfile(result3.get_target_file()):
            os.unlink(result3.get_target_file())

    def _read_file(self, upload: Upload, index: int) -> AResponse:
        handler = open(self._get_test_file(), 'rb')
        handler.seek(index * upload._bytes_per_part)
        content = bytearray(handler.read(upload._bytes_per_part))
        handler.close()
        return upload.partes_upload(content)
