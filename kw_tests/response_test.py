from .common_class import CommonTestClass
from kw_upload.exceptions import UploadException
from kw_upload.responses import InitResponse, CheckResponse, TruncateResponse
from kw_upload.responses import UploadResponse, DoneResponse, CancelResponse


class ResponseTest(CommonTestClass):

    def test_init_begin(self):
        lib = InitResponse.init_begin(self._mock_shared_key(), self._mock_data())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert 'abcdef' == lib.get_result()['name']
        assert InitResponse.STATUS_BEGIN == lib.get_result()['status']
        assert 12 == lib.get_result()['totalParts']
        assert 64 == lib.get_result()['partSize']
        assert 7 == lib.get_result()['lastKnownPart']

    def test_init_continue(self):
        lib = InitResponse.init_continue(self._mock_shared_key(), self._mock_data())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert 'abcdef' == lib.get_result()['name']
        assert InitResponse.STATUS_CONTINUE == lib.get_result()['status']

    def test_init_error(self):
        ex = UploadException('Testing one')
        lib = InitResponse.init_error(self._mock_shared_key(), self._mock_data(), ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert 'abcdef' == lib.get_result()['name']
        assert InitResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_init_continue_fail(self):
        lib = InitResponse.init_continue_fail(self._mock_shared_key(), self._mock_data(), 'Testing one')

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert 'abcdef' == lib.get_result()['name']
        assert InitResponse.STATUS_FAILED_CONTINUE == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_check_ok(self):
        lib = CheckResponse.init_ok(self._mock_shared_key(), '123abc456def789')

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert '123abc456def789' == lib.get_result()['checksum']
        assert CheckResponse.STATUS_OK == lib.get_result()['status']

    def test_check_error(self):
        ex = UploadException('Testing one')
        lib = CheckResponse.init_error(self._mock_shared_key(), ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert '' == lib.get_result()['checksum']
        assert CheckResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_truncate_ok(self):
        lib = TruncateResponse.init_ok(self._mock_shared_key())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert TruncateResponse.STATUS_OK == lib.get_result()['status']

    def test_truncate_error(self):
        ex = UploadException('Testing one')
        lib = TruncateResponse.init_error(self._mock_shared_key(), ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert TruncateResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_upload_ok(self):
        lib = UploadResponse.init_ok(self._mock_shared_key())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert UploadResponse.STATUS_OK == lib.get_result()['status']
        assert UploadResponse.STATUS_OK == lib.get_result()['errorMessage']

    def test_upload_complete(self):
        lib = UploadResponse.init_complete(self._mock_shared_key())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert UploadResponse.STATUS_COMPLETE == lib.get_result()['status']
        assert UploadResponse.STATUS_OK == lib.get_result()['errorMessage']

    def test_upload_fail(self):
        ex = UploadException('Testing one')
        lib = UploadResponse.init_error(self._mock_shared_key(), ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert UploadResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_done_complete(self):
        data = self._mock_data()
        lib = DoneResponse.init_done(self._mock_shared_key(), self._get_test_dir(), data)

        assert self._get_test_dir() + data.file_name == lib.get_target_file()
        assert self._mock_shared_key() == lib.get_result()['driver']
        assert UploadResponse.STATUS_COMPLETE == lib.get_result()['status']
        assert UploadResponse.STATUS_OK == lib.get_result()['errorMessage']

    def test_done_fail(self):
        data = self._mock_data()
        ex = UploadException('Testing one')
        lib = DoneResponse.init_error(self._mock_shared_key(), data, ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert UploadResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']

    def test_cancel_ok(self):
        lib = CancelResponse.init_cancel(self._mock_shared_key())

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert CancelResponse.STATUS_OK == lib.get_result()['status']

    def test_cancel_error(self):
        ex = UploadException('Testing one')
        lib = CancelResponse.init_error(self._mock_shared_key(), ex)

        assert self._mock_shared_key() == lib.get_result()['driver']
        assert CancelResponse.STATUS_FAIL == lib.get_result()['status']
        assert 'Testing one' == lib.get_result()['errorMessage']
