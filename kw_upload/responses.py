
from .drive_file import UploadData
from .exceptions import PortException


class AResponse:
    """
     * Responses for client
    """

    STATUS_OK = 'OK'
    STATUS_FAIL = 'FAIL'
    STATUS_COMPLETE = 'COMPLETE'
    STATUS_BEGIN = 'BEGIN'
    STATUS_CONTINUE = 'CONTINUE'
    STATUS_FAILED_CONTINUE = 'FAILED_CONTINUE'

    def __init__(self):
        super().__init__()
        self._shared_key = ''
        self._error_message = self.STATUS_OK
        self._status = self.STATUS_OK

    def get_result(self):
        raise NotImplementedError('TBI')


class CancelResponse(AResponse):
    """
     * Responses sent during upload cancelling
    """

    @staticmethod
    def init_cancel(shared_key: str):
        return CancelResponse().set_data(shared_key, CancelResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, ex: PortException):
        return CancelResponse().set_data(shared_key, CancelResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self):
        return {
            "driver": str(self._shared_key),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class CheckResponse(AResponse):
    """
     * Responses sent from content check
    """

    def __init__(self):
        super().__init__()
        self._checksum = ''

    @staticmethod
    def init_ok(shared_key: str, checksum: str):
        return CheckResponse().set_data(shared_key, checksum, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, ex: PortException):
        return CheckResponse().set_data(shared_key, '', AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, checksum: str, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._checksum = checksum
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self):
        return {
            "driver": str(self._shared_key),
            "checksum": str(self._checksum),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class DoneResponse(AResponse):
    """
     * Responses sent during upload closure
    """

    def __init__(self):
        super().__init__()
        self._data = None
        self._target_path = ''

    @staticmethod
    def init_done(shared_key: str, target_path: str, data: UploadData):
        return DoneResponse().set_data(shared_key, data, target_path, AResponse.STATUS_COMPLETE)

    @staticmethod
    def init_error(shared_key: str, data: UploadData, ex: PortException):
        return DoneResponse().set_data(shared_key, data, '', AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self,
                 shared_key: str,
                 data: UploadData,
                 target_path: str,
                 status: str,
                 error_message: str = AResponse.STATUS_OK
                 ):
        self._shared_key = shared_key
        self._target_path = target_path
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_target_file(self) -> str:
        return self._target_path + self._data.temp_name

    def get_file_name(self) -> str:
        return self._data.file_name

    def get_result(self):
        return {
            "name": str(self._data.file_name),
            "driver": str(self._shared_key),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class InitResponse(AResponse):
    """
     * Responses sent during upload initialization
    """

    def __init__(self):
        super().__init__()
        self._data = None

    @staticmethod
    def init_begin(shared_key: str, data: UploadData):
        return InitResponse().set_data(shared_key, data, AResponse.STATUS_BEGIN)

    @staticmethod
    def init_continue(shared_key: str, data: UploadData):
        return InitResponse().set_data(shared_key, data, AResponse.STATUS_CONTINUE)

    @staticmethod
    def init_error(shared_key: str, data: UploadData, ex: PortException):
        return InitResponse().set_data(shared_key, data, AResponse.STATUS_FAIL, ex.get_message())

    @staticmethod
    def init_continue_fail(shared_key: str, data: UploadData, message: str):
        return InitResponse().set_data(shared_key, data, AResponse.STATUS_FAILED_CONTINUE, message)

    def set_data(self, shared_key: str, data: UploadData, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self):
        return {
            "name": str(self._data.file_name),
            "driver": str(self._shared_key),
            "totalParts": int(self._data.parts_count),
            "lastKnownPart": int(self._data.last_known_part),
            "partSize": int(self._data.bytes_per_part),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class TruncateResponse(AResponse):
    """
     * Responses sent during file truncation
    """

    @staticmethod
    def init_ok(shared_key: str):
        return TruncateResponse().set_data(shared_key, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, ex: PortException):
        return TruncateResponse().set_data(shared_key, AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self):
        return {
            "driver": str(self._shared_key),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class UploadResponse(AResponse):

    @staticmethod
    def init_ok(shared_key: str):
        return UploadResponse().set_data(shared_key, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, ex: PortException):
        return UploadResponse().set_data(shared_key, AResponse.STATUS_FAIL, ex.get_message())

    @staticmethod
    def init_complete(shared_key: str):
        return UploadResponse().set_data(shared_key, AResponse.STATUS_COMPLETE)

    def set_data(self, shared_key: str, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self):
        return {
            "driver": str(self._shared_key),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }
