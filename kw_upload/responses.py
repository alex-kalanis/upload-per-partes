from .info_format import DataPack
from .exceptions import PortException


class AResponse:
    """
     * Responses for client
    """

    STATUS_OK = 'OK'
    STATUS_FAIL = 'FAIL'

    def __init__(self):
        super().__init__()
        self._shared_key = ''
        self._error_message = self.STATUS_OK
        self._status = self.STATUS_OK

    def get_result(self) -> dict:
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

    def get_result(self) -> dict:
        return {
            "sharedKey": str(self._shared_key),
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

    def get_result(self) -> dict:
        return {
            "sharedKey": str(self._shared_key),
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

    @staticmethod
    def init_done(shared_key: str, data: DataPack):
        return DoneResponse().set_data(shared_key, data, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, data: DataPack, ex: PortException):
        return DoneResponse().set_data(shared_key, data, AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self,
                 shared_key: str,
                 data: DataPack,
                 status: str,
                 error_message: str = AResponse.STATUS_OK
                 ):
        self._shared_key = shared_key
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_temporary_location(self) -> str:
        return self._data.temp_location

    def get_file_name(self) -> str:
        return self._data.file_name

    def get_result(self) -> dict:
        return {
            "name": str(self._data.file_name),
            "sharedKey": str(self._shared_key),
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
    def init_ok(shared_key: str, data: DataPack):
        return InitResponse().set_data(shared_key, data, AResponse.STATUS_OK)

    @staticmethod
    def init_error(data: DataPack, ex: PortException):
        return InitResponse().set_data('', data, AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, data: DataPack, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self) -> dict:
        return {
            "name": str(self._data.file_name),
            "sharedKey": str(self._shared_key),
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

    def __init__(self):
        super().__init__()
        self._data = None

    @staticmethod
    def init_ok(shared_key: str, data: DataPack):
        return TruncateResponse().set_data(shared_key, data, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, data: DataPack, ex: PortException):
        return TruncateResponse().set_data(shared_key, data, AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, data: DataPack, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self) -> dict:
        return {
            "sharedKey": str(self._shared_key),
            "lastKnownPart": int(self._data.last_known_part),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }


class UploadResponse(AResponse):

    def __init__(self):
        super().__init__()
        self._data = None

    @staticmethod
    def init_ok(shared_key: str, data: DataPack):
        return UploadResponse().set_data(shared_key, data, AResponse.STATUS_OK)

    @staticmethod
    def init_error(shared_key: str, data: DataPack, ex: PortException):
        return UploadResponse().set_data(shared_key, data, AResponse.STATUS_FAIL, ex.get_message())

    def set_data(self, shared_key: str, data: DataPack, status: str, error_message: str = AResponse.STATUS_OK):
        self._shared_key = shared_key
        self._data = data
        self._status = status
        self._error_message = error_message
        return self

    def get_result(self) -> dict:
        return {
            "sharedKey": str(self._shared_key),
            "lastKnownPart": int(self._data.last_known_part),
            "status": str(self._status),
            "errorMessage": str(self._error_message),
        }
