from kw_tests.common_class import CommonTestClass
from kw_tests.support import DataRam, InfoRam, Key, Strings, Files
from kw_upload.exceptions import UploadException, ContinuityUploadException
from kw_upload.data_storage import AStorage as DataStorage
from kw_upload.info_storage import AStorage as InfoStorage
from kw_upload.info_format import DataPack, Json
from kw_upload.upload import DriveFile, Processor, Uploader
from kw_upload.uploader.essentials import Calculates, Hashed, TargetSearch
from kw_upload.uploader.translations import Translations


class BasicTest(CommonTestClass):
    """
     * beware, i need to test this, because it's necessary for run - it happens for me to got failed testing
     * PHP has problems, it cost me 3hrs, python is no better - it's based absolutely from start, not by pointer
    """
    def test_strings(self):
        assert 'bcdef'  == 'abcdef'[1:]
        assert 'bcd'    == 'abcdef'[1:4]
        assert 'abcd'   == 'abcdef'[0:4]
        assert 'abcdef' == 'abcdef'[0:8]
        assert 'f'      == 'abcdef'[-1:8]
        assert 'f'      == 'abcdef'[-1:]

        # now with lib
        assert 'bcdef'  == Strings.substr('abcdef', 1, None)
        assert 'bcd'    == Strings.substr('abcdef', 1, 3)
        assert 'abcd'   == Strings.substr('abcdef', 0, 4)
        assert 'abcdef' == Strings.substr('abcdef', 0, 8)
        assert 'f'      == Strings.substr('abcdef', -1, 1)
        assert 'f'      == Strings.substr('abcdef', -1, None)

    def test_calculate(self):
        lib = Calculates.init()
        assert Calculates.DEFAULT_BYTES_PER_PART == lib.get_bytes_per_part()

        lib2 = Calculates.init(20)
        assert 20 == lib2.get_bytes_per_part()
        assert  2 == lib2.calc_parts(35)
        assert  2 == lib2.calc_parts(40)
        assert  3 == lib2.calc_parts(41)


class DriveFileTest(CommonTestClass):

    def tearDown(self):
        drive_file = self._get_drive_file()
        if drive_file.exists(self._mock_key()):
            drive_file.remove(self._mock_key())
        super().tearDown()

    def test_thru(self):
        drive_file = self._get_drive_file()
        assert drive_file.write(self._mock_key(), self._mock_data())
        data = drive_file.read(self._mock_key())
        assert isinstance(data, DataPack)
        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_location
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 7 == data.last_known_part
        drive_file.remove(self._mock_key())

    def test_write_fail(self):
        drive_file = self._get_drive_file()
        data = self._mock_data()
        assert drive_file.write(self._mock_key(), data)
        assert drive_file.exists(self._mock_key())
        try:
            drive_file.write(self._mock_key(), data, True)
            assert False, 'Drive file overwrite'
        except ContinuityUploadException as ex:
            assert 'DRIVEFILE ALREADY EXISTS' == ex.get_message()

    def test_update(self):
        drive_file = self._get_drive_file()
        data = self._mock_data()
        assert drive_file.write(self._mock_key(), data)
        assert drive_file.update_last_part(self._mock_key(), data, data.last_known_part + 1)
        drive_file.remove(self._mock_key())

    def test_update_fail(self):
        drive_file = self._get_drive_file()
        data = self._mock_data()
        assert drive_file.write(self._mock_key(), data)
        try:
            drive_file.update_last_part(self._mock_key(), data, data.last_known_part + 5)
            assert False, 'Last part updated'
        except UploadException as ex:
            assert 'DRIVEFILE IS NOT CONTINUOUS' == ex.get_message()

    def _mock_key(self) -> str:
        return 'fghjkl' + TargetSearch.FILE_DRIVER_SUFF

    def _get_drive_file(self) -> DriveFile:
        lang = Translations()
        storage = InfoRam(lang)
        target = TargetSearch(lang, storage, DataRam(lang))
        return DriveFile(lang, storage, Json(), Key(lang, target))


class ProcessorTest(CommonTestClass):

    def __init__(self, methodName='runTest'):
        super().__init__(methodName)
        _lang = Translations()
        self._info_storage = InfoRam(_lang)
        self._data_storage = DataRam(_lang)
        target = TargetSearch(_lang, self._info_storage, self._data_storage)
        self._drive_file = DriveFile(_lang, self._info_storage, Json(), Key(_lang, target))
        self._processor = Processor(_lang, self._drive_file, self._data_storage, Hashed())

    def tearDown(self):
        if self._drive_file.exists(self._mock_key()):
            self._drive_file.remove(self._mock_key())
        super().tearDown()

    def test_init(self):
        pack = self._mock_data()
        pack.last_known_part = 5
        data = self._processor.init(pack, self._mock_shared_key())

        assert isinstance(data, DataPack)
        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_location
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 5 == data.last_known_part

        data2 = self._processor.done(self._mock_key())
        assert isinstance(data2, DataPack)
        assert 'abcdef' == data.file_name
        assert self._get_test_dir() + 'abcdef' == data.temp_location
        assert 123456 == data.file_size
        assert 12 == data.parts_count
        assert 64 == data.bytes_per_part
        assert 5 == data.last_known_part
        self._clear()

    def test_init_fail(self):
        pack = self._mock_data()
        pack.last_known_part = 4
        data = self._processor.init(pack, self._mock_shared_key())

        assert isinstance(data, DataPack)
        assert 4 == data.last_known_part

        pack.last_known_part = 8
        data2 = self._processor.init(pack, self._mock_shared_key())
        assert 4 == data2.last_known_part
        assert 8 != data2.last_known_part
        self._clear()

    def test_upload_early(self):
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 4
        pack.parts_count = 8
        self._processor.init(pack, self._mock_shared_key())
        data_cont = b'asdfghjklyxcvbnmqwertzuiop1234567890'
        self._processor.upload(self._mock_shared_key(), data_cont, 5)  # pass, last is 4, wanted 5
        try:
            self._processor.upload(self._mock_shared_key(), data_cont, 7)  # fail, last is 5, wanted 6
            assert False, 'Early reading processed'
        except UploadException as ex:
            assert 'READ TOO EARLY' == ex.get_message()
        self._clear()

    def test_check_segment_sub_zero(self):
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 4
        pack.parts_count = 8
        self._processor.init(pack, self._mock_shared_key())
        try:
            self._processor.check(self._mock_shared_key(), -5)  # fail, sub zero
            assert False, 'Sub zero wins'
        except UploadException as ex:
            assert 'SEGMENT OUT OF BOUNDS' == ex.get_message()
        self._clear()

    def test_check_segment_available_parts(self):
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 4
        pack.parts_count = 8
        self._processor.init(pack, self._mock_shared_key())
        try:
            self._processor.check(self._mock_shared_key(), 10)  # fail, out of size
            assert False, 'Overweight segment'
        except UploadException as ex:
            assert 'SEGMENT OUT OF BOUNDS' == ex.get_message()
        self._clear()

    def test_check_segment_not_uploaded(self):
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 4
        pack.parts_count = 8
        self._processor.init(pack, self._mock_shared_key())
        try:
            self._processor.check(self._mock_shared_key(), 6)  # fail, outside upload
            assert False, 'Outside upload'
        except UploadException as ex:
            assert 'SEGMENT NOT UPLOADED YET' == ex.get_message()
        self._clear()

    def test_simple_thru(self):
        from hashlib import md5
        cont = Files.file_get_contents(self._get_test_file(), 80)
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 4
        pack.parts_count = 8
        data = self._processor.init(pack, self._mock_shared_key())
        data_cont = Strings.substr(cont, 0, 30) + 'asdfghjklyxcvbnmqwer'
        # set problematic content
        self._processor.upload(self._mock_shared_key(), bytes(data_cont, encoding='utf-8'))
        # now checks
        for i in range(0, data.last_known_part + 1):
            remote_md5 = self._processor.check(self._mock_shared_key(), i)
            local_str = Strings.substr(cont, i * data.bytes_per_part, data.bytes_per_part)
            local_md5 = md5(local_str.encode()).hexdigest()
            if remote_md5 != local_md5:
                self._processor.truncate_from(self._mock_shared_key(), i)
                break

        data2 = self._drive_file.read(self._mock_shared_key())
        assert 3 == data2.last_known_part

        # set rest
        for i in range(data2.last_known_part + 1, data2.parts_count):
            data_pack = Strings.substr(str(cont), i * data2.last_known_part, data2.bytes_per_part)
            self._processor.upload(self._mock_shared_key(), bytes(data_pack, encoding='utf-8'))

        self._processor.cancel(self._mock_shared_key())  # intended, because pass will be checked in upload itself
        self._clear()

    def test_simple_all(self):
        cont = Files.file_get_contents(self._get_test_file(), 80)
        pack = self._mock_data()
        pack.file_size = 80
        pack.bytes_per_part = 10
        pack.last_known_part = 7
        pack.parts_count = 8
        self._processor.init(pack, self._mock_shared_key())
        self._processor.upload(self._mock_shared_key(), bytes(cont, encoding='utf-8'))
        data = self._processor.done(self._mock_shared_key())
        assert 8 == data.last_known_part
        assert 8 == data.parts_count
        self._clear()

    def _mock_key(self) -> str:
        return 'fghjkl' + TargetSearch.FILE_DRIVER_SUFF

    def _clear(self):
        self._data_storage.remove(self._mock_shared_key())
        self._info_storage.remove(self._mock_shared_key())


class UploadTest(CommonTestClass):

    def test_simple_upload(self):
        from kw_upload.responses import InitResponse, UploadResponse, DoneResponse
        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 8000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        bytes_per_part = result1.get_result()['partSize']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care
        assert 1024 == bytes_per_part

        # step 2 - send data
        i = 0
        while True:
            if i * bytes_per_part > max_size:
                break
            part = Strings.substr(content, i * bytes_per_part, bytes_per_part)
            result2 = lib.upload(shared_key, bytes(part, encoding='utf-8'))
            assert UploadResponse.STATUS_OK == result2.get_result()['status']
            i = i + 1

        # step 3 - close upload
        target = lib.get_lib_driver().read(shared_key).temp_location
        result3 = lib.done(shared_key)
        assert DoneResponse.STATUS_OK == result3.get_result()['status']

        # check content
        uploaded = lib.get_storage().get_all(target)
        assert 0 < len(uploaded)
        assert content == uploaded.decode()

    def test_stopped_upload(self):
        from kw_upload.responses import InitResponse, UploadResponse, DoneResponse
        import hashlib
        import math
        # from pprint import pprint

        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 900000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        bytes_per_part = result1.get_result()['partSize']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care
        assert 1024 == bytes_per_part
        assert 629 == result1.get_result()['totalParts']

        # step 2 - send first part of data
        i = 0
        limited = math.floor(max_size / 2)
        while True:
            if i * bytes_per_part > limited:
                break
            part = Strings.substr(content, i * bytes_per_part, bytes_per_part)
            result2 = lib.upload(shared_key, bytes(part, encoding='utf-8'))
            assert UploadResponse.STATUS_OK == result2.get_result()['status']
            i = i + 1

        # step 3 - again from the beginning
        result3 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result3.get_result()['status']
        bytes_per_part = result3.get_result()['partSize']
        last_known_part = result3.get_result()['lastKnownPart']
        shared_key = result3.get_result()['sharedKey']  # for this test it's zero care
        assert 315 == last_known_part  # NOT ZERO
        assert 1024 == bytes_per_part

        # step 4 - check first part
        i = 0
        while True:
            if i > last_known_part:
                break
            part = Strings.substr(content, i * bytes_per_part, bytes_per_part)
            result4 = lib.check(shared_key, i)
            assert UploadResponse.STATUS_OK == result4.get_result()['status']
            if hashlib.md5(part.encode('utf-8')) != result4.get_result()['checksum']:
                # step 5 - truncate of failed part
                result5 = lib.truncate_from(shared_key, i - 2)
                assert UploadResponse.STATUS_OK == result5.get_result()['status']
                break
            else :
                assert hashlib.md5(part.encode('utf-8')) == result4.get_result()['checksum']
            i = i + 1

        last_known_part = result5.get_result()['lastKnownPart']
        assert 313 == last_known_part

        # step 6 - send second part
        i = last_known_part
        while True:
            if i * bytes_per_part > max_size:
                break
            part = Strings.substr(content, i * bytes_per_part, bytes_per_part)
            result6 = lib.upload(shared_key, bytes(part, encoding='utf-8'))
            assert UploadResponse.STATUS_OK == result6.get_result()['status']
            i = i + 1

        # step 7 - close upload
        target = lib.get_lib_driver().read(shared_key).temp_location
        result7 = lib.done(shared_key)
        assert DoneResponse.STATUS_OK == result7.get_result()['status']

        # check content
        uploaded = lib.get_storage().get_all(target)
        assert 0 < len(uploaded)
        assert content == uploaded.decode()

    def test_cancel(self):
        from kw_upload.responses import InitResponse, UploadResponse, CancelResponse
        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 8000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care

        # step 2 - send data
        result2 = lib.upload(shared_key, bytes(content, encoding='utf-8'))  # flush it all
        assert UploadResponse.STATUS_OK == result2.get_result()['status']

        # step 3 - cancel upload
        target = lib.get_lib_driver().read(shared_key).temp_location
        result3 = lib.cancel(shared_key)
        assert CancelResponse.STATUS_OK == result3.get_result()['status']

        # check content
        assert not lib.get_storage().get_all(target)

    def test_init_fail(self):
        from kw_upload.responses import InitResponse
        lib = UploaderMock()
        # init data - but there is failure
        result = lib.init('', '', 123456)
        assert InitResponse.STATUS_FAIL == result.get_result()['status']

    def test_check_fail(self):
        from kw_upload.responses import InitResponse, UploadResponse, CancelResponse
        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 8000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care

        # step 2 - check data - non existing segment
        result2 = lib.check(shared_key, 35)
        assert UploadResponse.STATUS_FAIL == result2.get_result()['status']

        # step 3 - cancel upload
        result3 = lib.cancel(shared_key)
        assert CancelResponse.STATUS_OK == result3.get_result()['status']

    def test_truncate_fail(self):
        from kw_upload.responses import InitResponse, UploadResponse, CancelResponse
        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 8000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care

        # step 2 - truncate data - non existing segment
        result2 = lib.truncate_from(shared_key, 35)
        assert UploadResponse.STATUS_FAIL == result2.get_result()['status']

        # step 3 - cancel upload
        result3 = lib.cancel(shared_key)
        assert CancelResponse.STATUS_OK == result3.get_result()['status']

    def test_upload_fail(self):
        from kw_upload.responses import InitResponse, UploadResponse, CancelResponse
        lib = UploaderMock()  # must stay same, because it's only in the ram
        content = Files.file_get_contents(self._get_test_file(), 8000)  # read test content into ram
        max_size = len(content)

        # step 1 - init driver
        result1 = lib.init(self._get_test_dir(), 'lorem-ipsum.txt', max_size)
        assert InitResponse.STATUS_OK == result1.get_result()['status']
        shared_key = result1.get_result()['sharedKey']  # for this test it's zero care

        # step 2 - upload data - not continuous
        result2 = lib.upload(shared_key, bytes(Strings.substr(content, 23, 47568), encoding='utf-8'), 66)
        assert UploadResponse.STATUS_FAIL == result2.get_result()['status']

        # step 3 - cancel upload
        result3 = lib.cancel(shared_key)
        assert CancelResponse.STATUS_OK == result3.get_result()['status']

    def test_cancel_fail(self):
        from kw_upload.responses import CancelResponse
        lib = UploaderMock()
        # cancel data - but there is nothing
        result = lib.cancel('qwertzuiop')
        assert CancelResponse.STATUS_FAIL == result.get_result()['status']

    def test_done_fail(self):
        from kw_upload.responses import DoneResponse
        lib = UploaderMock()
        # done data - but there is nothing
        result = lib.done('qwertzuiop')
        assert DoneResponse.STATUS_FAIL == result.get_result()['status']


class UploaderMock(Uploader):

    def _get_info_storage(self, lang: Translations) -> InfoStorage:
        super()._get_info_storage(lang)
        return InfoRam(lang)

    def _get_data_storage(self, lang: Translations) -> DataStorage:
        super()._get_data_storage(lang)
        return DataRam(lang)

    def _get_calc(self) -> Calculates:
        super()._get_calc()
        return Calculates(1024)

    def get_storage(self) -> DataStorage:
        return self._data_storage

    def get_lib_driver(self) -> DriveFile:
        return self._driver
