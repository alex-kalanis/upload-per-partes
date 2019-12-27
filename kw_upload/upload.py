import re
import os
from hashlib import md5
from .drive_file import ADriveFile, DriveFile
from .exceptions import UploadException, ContinuityUploadException
from .translations import Translations
from .responses import *


class Upload:
    """
     * Main server library for drive upload per-partes
    """

    FILE_DRIVER_SUFF = '.partial'
    FILE_UPLOAD_SUFF = '.upload'
    FILE_SUFF_SEP = '.'
    FILE_VER_SEP = '_'

    def __init__(self, target_path: str, shared_key: str = None):
        """
        :param target_path: str
        :param shared_key: str|None
        :raise: UploadException
        """
        self._target_path = '/'
        self._shared_key = ''
        self._driver = None
        self._bytes_per_part = 262144  # 1024*256
        self._target_path = target_path
        self._lang = self._get_translation()
        if shared_key:
            self._shared_key = shared_key
            self._init_driver(shared_key)

    def _get_translation(self) -> Translations:
        return Translations()

    def partes_cancel(self) -> CancelResponse:
        """
         * Upload file by parts, final status
        :return: CancelResponse
        """
        try:
            drive_data = self._driver.read()
            try:
                os.unlink(drive_data.temp_path)
            except IOError:
                raise UploadException(self._lang.cannot_remove_data())
            self._driver.remove()
            return CancelResponse.init_cancel(self._shared_key)
        except UploadException as ex:
            return CancelResponse.init_error(self._shared_key, ex)

    def partes_done(self) -> DoneResponse:
        """
         * Upload file by parts, final status
        :return: DoneResponse
        """
        try:
            drive_data = self._driver.read()
            self._driver.remove()
            return DoneResponse.init_done(self._shared_key, drive_data)
        except UploadException as ex:
            return DoneResponse.init_error(self._shared_key, UploadData(), ex)

    def partes_upload(self, content: bytearray, segment: int = None) -> AResponse:
        """
         * Upload file by parts, use driving file
        :param content: bytearray binary content
        :param segment: int|None where it save
        :return: AResponse
        """
        try:
            drive_data = self._driver.read()

            if not segment:
                segment = drive_data.last_known_part + 1
                self._save_file_part(drive_data, content)
                self._driver.update_last_part(drive_data, segment)
            else:
                if segment > drive_data.last_known_part + 1:
                    raise UploadException(self._lang.read_too_early())
                self._save_file_part(drive_data, content, segment * drive_data.bytes_per_part)

            return UploadResponse.init_ok(self._shared_key, drive_data)
        except UploadException as ex:
            return UploadResponse.init_error(self._shared_key, UploadData(), ex)

    def partes_truncate_from(self, segment: int) -> AResponse:
        """
         * Delete problematic segments
        :param segment: int
        :return: AResponse
        """
        try:
            return TruncateResponse.init_ok(self._shared_key, self._partes_truncate_from_part(segment))
        except UploadException as ex:
            return TruncateResponse.init_error(self._shared_key, UploadData(), ex)

    def partes_check(self, segment: int) -> AResponse:
        """
         * Check already uploaded parts
        :param segment: int
        :return: AResponse
        """
        try:
            return CheckResponse.init_ok(self._shared_key, self._partes_checksum_part(segment))
        except UploadException as ex:
            return CheckResponse.init_error(self._shared_key, ex)

    def partes_init(self, remote_file_name: str, length: int) -> AResponse:
        """
         * Upload file by parts, create driving file
        :param remote_file_name: str posted file name
        :param length: int complete file size
        :return: AResponse
        """
        file_name = self._find_name(remote_file_name)
        shared_key = self._get_shared_key(file_name)
        temp_path = self._target_path + self._get_temp_file_name(file_name)
        parts_counter = self._calc_parts(length)
        try:
            self._init_driver(shared_key)
            data_pack = UploadData().set_data(file_name, temp_path, length, parts_counter, self._bytes_per_part)
            try:
                self._driver.create(data_pack)
            except ContinuityUploadException:  # continue from previous?
                data_pack = self._driver.read()
            return InitResponse.init_ok(shared_key, data_pack)

        except UploadException as ex:  # something bad happen
            return InitResponse.init_error(shared_key, UploadData().set_data(
                file_name, temp_path, length, parts_counter, self._bytes_per_part, 0
            ), ex)

    def _find_name(self, name: str) -> str:
        """
         * Find non-existing name
        :param name: str
        :return: str
        """
        name = self._canonize(name)
        suffix = self._file_suffix(name)
        file_base = self._file_base(name)
        if os.path.isfile(self._target_path + name)\
                and not os.path.isfile(self._target_path + name + self.FILE_DRIVER_SUFF):
            i = 0
            while (
                os.path.isfile(self._target_path + file_base + self.FILE_VER_SEP + str(i) + self.FILE_SUFF_SEP + suffix)
            ):
                i += 1
            return file_base + self.FILE_VER_SEP + str(i) + self.FILE_SUFF_SEP + suffix
        else:
            return name

    def _canonize(self, file_name: str) -> str:
        f = re.sub(r'/((&[a-zA-Z]{1,6};)|(&#[a-zA-Z0-9]{1,7};))/', '', file_name)
        f = re.sub(r'#[^a-zA-Z0-9-_\s\.]#', '', f)  # remove non-alnum + dots
        f = re.sub(r'#[\s]#', '_', f)  # whitespaces to underscore
        file_suffix = self._file_suffix(f)
        file_base = self._file_base(f)
        name_length = len(file_suffix)
        if not name_length:
            return file_name[0:127]  # win limit...
        c = file_base[0: 127 - name_length]
        return c + self.FILE_SUFF_SEP + file_suffix

    def _file_suffix(self, file_name: str) -> str:
        try:
            pos = file_name.rindex(self.FILE_SUFF_SEP)
            return file_name[pos + 1:] if (0 < pos) else ''
        except ValueError:
            return ''

    def _file_base(self, file_name: str) -> str:
        try:
            pos = file_name.rindex(self.FILE_SUFF_SEP)
            return file_name[0:pos] if (0 < pos) else file_name[1:]
        except ValueError:
            return file_name

    def _calc_parts(self, length: int) -> int:
        parts_counter = int(length / self._bytes_per_part)
        return int(parts_counter) if (length % self._bytes_per_part) == 0 else int(parts_counter + 1)

    def _init_driver(self, shared_key: str):
        self._driver = DriveFile(self._lang, ADriveFile.init(
            self._lang,
            self._get_driver_variant(),
            self._target_path + shared_key
        ))

    def _get_driver_variant(self) -> int:
        return ADriveFile.VARIANT_TEXT

    def _get_shared_key(self, file_name: str) -> str:
        return self._file_base(file_name) + self.FILE_DRIVER_SUFF

    def _get_temp_file_name(self, file_name: str) -> str:
        return self._file_base(file_name) + self.FILE_UPLOAD_SUFF

    def _save_file_part(self, data: UploadData, content: bytearray, seek: int = None) -> bool:
        """
        :param data: Data
        :param content: bytearray binary content
        :param seek: int|None
        :return: bool
        :raise UploadException:
        """
        if seek:
            try:
                handle = open(data.temp_path, 'wb')
            except IOError:
                raise UploadException(self._lang.cannot_open_file())
            try:
                handle.seek(seek)
            except IOError:
                raise UploadException(self._lang.cannot_seek_file())
            try:
                handle.write(content)
            except IOError:
                raise UploadException(self._lang.cannot_write_file())
            handle.close()
        else:
            try:
                handle = open(data.temp_path, 'ab')
            except IOError:
                raise UploadException(self._lang.cannot_open_file())
            try:
                handle.write(content)
            except IOError:
                raise UploadException(self._lang.cannot_write_file())
            handle.close()
        return True

    def _partes_checksum_part(self, segment: int) -> str:
        """
        :param segment: int where it will be
        :return: str
        :raise UploadException:
        """
        data = self._driver.read()
        self._check_segment(data, segment)
        try:
            handle = open(data.temp_path)
            handle.seek(data.bytes_per_part * segment)
            content = handle.read(data.bytes_per_part)
            handle.close()
            return md5(content)
        except IOError:
            raise UploadException(self._lang.segment_out_of_bounds())

    def _partes_truncate_from_part(self, segment: int) -> UploadData:
        """
        :param segment: int where it will be
        :return UploadData:
        :raise UploadException:
        """
        data = self._driver.read()
        self._check_segment(data, segment)
        handle = None
        try:
            handle = open(data.temp_path, 'rw+')
            handle.truncate(data.bytes_per_part * segment)
            handle.seek(0)
            handle.close()
        except IOError:
            if handle:
                handle.close()
            raise UploadException(self._lang.cannot_truncate_file())

        self._driver.update_last_part(data, segment, False)
        return data

    def _check_segment(self, data: UploadData, segment: int):
        """
        :param data: Data
        :param segment: int
        :return:
        :raise UploadException:
        """
        if segment < 0:
            raise UploadException(self._lang.segment_out_of_bounds())
        if segment > data.parts_count:
            raise UploadException(self._lang.segment_out_of_bounds())
        if segment > data.last_known_part:
            raise UploadException(self._lang.segment_not_uploaded_yet())
