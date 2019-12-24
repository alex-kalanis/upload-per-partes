
class PortException(Exception):
    """
    Port exception system from PHP
    It has good messages
    """
    def __init__(self, message: str, code: int = 0, *args, **kwargs):
        super().__init__(self, args, kwargs)
        self.message = message
        self.code = code

    def get_message(self) -> str:
        return self.message

    def get_code(self) -> int:
        return self.code


class UploadException(PortException):
    """
     * Dead upload exception
    """
    pass


class ContinuityUploadException(UploadException):
    """
     * Dead upload exception - we have old one for continue
    """
    pass
