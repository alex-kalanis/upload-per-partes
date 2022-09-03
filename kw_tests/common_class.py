import os
import unittest
from kw_upload.uploader.data import DataPack


class CommonTestClass(unittest.TestCase):

    def _mock_test_file(self) -> str:
        return self._get_test_dir() + 'testing.partial'

    def _mock_shared_key(self) -> str:
        return 'driver.partial'

    def _get_test_dir(self) -> str:
        return os.path.realpath(os.path.dirname(__file__) + '/../php-tests/tmp/') + '/'

    def _get_test_file(self) -> str:
        return os.path.realpath(os.path.dirname(__file__) + '/../php-tests/testing-ipsum.txt')

    def _mock_data(self) -> DataPack:
        return DataPack().set_data(
            'abcdef',
            self._get_test_dir() + 'abcdef',
            123456,
            12,
            64,
            7
        )
