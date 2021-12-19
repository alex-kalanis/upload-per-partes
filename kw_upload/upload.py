from .data_storage import AStorage as DataStorage
from .exceptions import UploadException, ContinuityUploadException
from .info_format import AFormat, DataPack
from .info_storage import AStorage as InfoStorage
from .keys import AKey
from .responses import DoneResponse, CancelResponse, UploadResponse, TruncateResponse, CheckResponse, InitResponse
from .uploader.essentials import Calculates, Hashed, TargetSearch
from .uploader.translations import Translations


class DriveFile:
    """
     * Processing drive file
    """

    def __init__(self, lang: Translations, storage: InfoStorage, formatted: AFormat, key: AKey):
        self._storage = storage
        self._format = formatted
        self._lang = lang
        self._key = key

    def write(self, shared_key: str, data_pack: DataPack, is_new: bool = False) -> bool:
        """
         * Create new drive file
        :param shared_key:
        :param data_pack:
        :param is_new:
        :return: bool
        :raise: UploadException
        :raise: ContinuityUploadException
        """
        if is_new and self.exists(shared_key):
            raise ContinuityUploadException(self._lang.drive_file_already_exists())
        self._storage.save(self._key.from_shared_key(shared_key), self._format.to_format(data_pack))
        return True

    def read(self, shared_key: str) -> DataPack:
        """
         * Read drive file
        :param shared_key:
        :return: DataPack
        :raise: UploadException
        """
        return self._format.from_format(self._storage.load(self._key.from_shared_key(shared_key)))

    def update_last_part(self, shared_key: str, data_pack: DataPack, last: int, check_continuous: bool = True) -> bool:
        """
         * Update upload info
        :param shared_key:
        :param data_pack:
        :param last:
        :param check_continuous:
        :return: bool
        :raise: UploadException
        """
        if check_continuous:
            if (data_pack.last_known_part + 1) != last:
                raise UploadException(self._lang.drive_file_not_continuous())

        data_pack.last_known_part = last
        self._storage.save(self._key.from_shared_key(shared_key), self._format.to_format(data_pack))
        return True

    def remove(self, shared_key: str) -> bool:
        """
         * Delete drive file - usually on finish or discard
        :param shared_key:
        :return: bool
        :raise: UploadException
        """
        self._storage.remove(self._key.from_shared_key(shared_key))
        return True

    def exists(self, shared_key: str) -> bool:
        """
         * Has driver data? Mainly for testing
        :param shared_key:
        :return: bool
        """
        return self._storage.exists(self._key.from_shared_key(shared_key))


class Processor:
    """
     * Processing upload per-partes
    """

    def __init__(self, lang: Translations, driver: DriveFile, storage: DataStorage, hashed: Hashed):
        self._lang = lang
        self._driver = driver
        self._storage = storage
        self._hashed = hashed

    def cancel(self, shared_key: str):
        """
         * Upload file by parts, final status - cancel that
        :param shared_key:
        :return: void
        :raise: UploadException
        """
        data_pack = self._driver.read(shared_key)
        self._storage.remove(data_pack.temp_location)
        self._driver.remove(shared_key)

    def done(self, shared_key: str) -> DataPack:
        """
         * Upload file by parts, final status
        :param shared_key:
        :return: DataPack
        :raise: UploadException
        """
        data_pack = self._driver.read(shared_key)
        self._driver.remove(shared_key)
        return data_pack

    def upload(self, shared_key: str, content: bytes, segment: int = None) -> DataPack:
        """
         * Upload file by parts, use driving file
        :param shared_key:
        :param content:
        :param segment:
        :return: DataPack
        :raise: UploadException
        """
        data_pack = self._driver.read(shared_key)

        if segment:
            if segment > data_pack.last_known_part + 1:
                raise UploadException(self._lang.read_too_early())
            self._storage.add_part(data_pack.temp_location, content, segment * data_pack.bytes_per_part)
        else:
            segment = data_pack.last_known_part + 1
            self._storage.add_part(data_pack.temp_location, content)
            self._driver.update_last_part(shared_key, data_pack, segment)

        return data_pack

    def truncate_from(self, shared_key: str, segment: int) -> DataPack:
        """
         * Delete problematic segments
        :param shared_key:
        :param segment:
        :return: DataPack
        :raise: UploadException
        """
        data_pack = self._driver.read(shared_key)
        self._check_segment(data_pack, segment)
        self._storage.truncate(data_pack.temp_location, data_pack.bytes_per_part * segment)
        self._driver.update_last_part(shared_key, data_pack, segment, False)
        return data_pack

    def check(self, shared_key: str, segment: int) -> str:
        """
         * Check already uploaded parts
        :param shared_key:
        :param segment:
        :return: str
        :raise: UploadException
        """
        data_pack = self._driver.read(shared_key)
        self._check_segment(data_pack, segment)
        return self._hashed.calc_hash(self._storage.get_part(
            data_pack.temp_location, data_pack.bytes_per_part * segment, data_pack.bytes_per_part
        ))

    def init(self, data_pack: DataPack, shared_key: str) -> DataPack:
        """
         * Upload file by parts, create driving file, returns correct one (because it can exist)
        :param data_pack:
        :param shared_key:
        :return: DataPack
        :raise: UploadException
        """
        try:
            self._driver.write(shared_key, data_pack, True)
        except ContinuityUploadException:  # continuity from previous try - we got datapack, so we return it
            data_pack = self._driver.read(shared_key)
        return data_pack

    def _check_segment(self, data_pack: DataPack, segment: int):
        """
        :param data_pack:
        :param segment:
        :return: void
        :raise: UploadException
        """
        if segment < 0:
            raise UploadException(self._lang.segment_out_of_bounds())
        if segment > data_pack.parts_count:
            raise UploadException(self._lang.segment_out_of_bounds())
        if segment > data_pack.last_known_part:
            raise UploadException(self._lang.segment_not_uploaded_yet())


class Uploader:
    """
     * Main server library for drive upload per-partes
    """

    def __init__(self):
        _lang = self._get_translations()
        self._info_storage = self._get_info_storage(_lang)
        self._data_storage = self._get_data_storage(_lang)
        _format = AFormat.get_format(_lang, self._get_format())
        self._target_search = self._get_target(_lang)
        self._calculations = self._get_calc()
        self._hashed = self._get_hashed()
        self._key = AKey.get_variant(_lang, self._target_search, self._get_key_variant())
        _driver = DriveFile(_lang, self._info_storage, _format, self._key)
        self._processor = self._get_processor(_lang, _driver, self._data_storage, self._hashed)

    def _get_format(self) -> int:
        return AFormat.FORMAT_JSON

    def _get_key_variant(self) -> int:
        return AKey.VARIANT_VOLUME

    def _get_translations(self) -> Translations:
        return Translations()

    def _get_info_storage(self, lang: Translations) -> InfoStorage:
        from .info_storage import Volume
        return Volume(lang)

    def _get_data_storage(self, lang: Translations) -> DataStorage:
        from .data_storage import VolumeBasic
        return VolumeBasic(lang)

    def _get_target(self, lang: Translations) -> TargetSearch:
        return TargetSearch(lang)

    def _get_calc(self) -> Calculates:
        return Calculates(262144)

    def _get_hashed(self) -> Hashed:
        return Hashed()

    def _get_processor(self, lang: Translations, driver: DriveFile, storage: DataStorage, hashed: Hashed) -> Processor:
        return Processor(lang, driver, storage, hashed)

    def cancel(self, shared_key: str) -> CancelResponse:
        """
         * Upload file by parts, final status - cancel that
        :param shared_key:
        :return: CancelResponse
        """
        try:
            self._processor.cancel(shared_key)
            return CancelResponse.init_cancel(shared_key)
        except UploadException as ex:
            return CancelResponse.init_error(shared_key, ex)

    def done(self, shared_key: str) -> DoneResponse:
        """
         * Upload file by parts, final status
        :param shared_key:
        :return: DoneResponse
        """
        try:
            return DoneResponse.init_done(shared_key, self._processor.done(shared_key))
        except UploadException as ex:
            return DoneResponse.init_error(shared_key, DataPack.init(), ex)

    def upload(self, shared_key: str, content: bytes, segment: int = None) -> UploadResponse:
        """
         * Upload file by parts, use driving file
        :param shared_key:
        :param content:
        :param segment:
        :return: UploadResponse
        """
        try:
            return UploadResponse.init_ok(shared_key, self._processor.upload(shared_key, content, segment))
        except UploadException as ex:
            return UploadResponse.init_error(shared_key, DataPack.init(), ex)

    def truncate_from(self, shared_key: str, segment: int) -> TruncateResponse:
        """
         * Delete problematic segments
        :param shared_key:
        :param segment:
        :return: TruncateResponse
        """
        try:
            return TruncateResponse.init_ok(shared_key, self._processor.truncate_from(shared_key, segment))
        except UploadException as ex:
            return TruncateResponse.init_error(shared_key, DataPack.init(), ex)

    def check(self, shared_key: str, segment: int) -> CheckResponse:
        """
         * Check already uploaded parts
        :param shared_key:
        :param segment:
        :return: CheckResponse
        """
        try:
            return CheckResponse.init_ok(shared_key, self._processor.check(shared_key, segment))
        except UploadException as ex:
            return CheckResponse.init_error(shared_key, ex)

    def init(self, target_path: str, remote_file_name: str, length: int) -> InitResponse:
        """
         * Upload file by parts, create driving file
        :param target_path:
        :param remote_file_name:
        :param length:
        :return: InitResponse
        """
        parts_counter = self._calculations.calc_parts(length)
        try:
            self._target_search.set_target_dir(target_path).set_remote_file_name(remote_file_name).process()
            self._key.generate_keys()
            data_pack = DataPack.init().set_data(
                self._target_search.get_final_target_name(),
                self._target_search.get_temporary_target_location(),
                length,
                parts_counter,
                self._calculations.get_bytes_per_part()
            )
            return InitResponse.init_ok(
                self._key.get_shared_key(), self._processor.init(data_pack, self._key.get_shared_key())
            )
        except UploadException as ex:
            return InitResponse.init_error(DataPack.init().set_data(
                remote_file_name, '', length, parts_counter, self._calculations.get_bytes_per_part()
            ), ex)
