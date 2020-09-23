from .uploader.essentials import TargetSearch
from .uploader.translations import Translations
from .exceptions import UploadException


class AKey:

    VARIANT_VOLUME = 1
    VARIANT_RANDOM = 2
    VARIANT_REDIS = 3

    def __init__(self, lang: Translations, target: TargetSearch):
        self._lang = lang
        self._target = target
        self._shared_key = ''

    def from_shared_key(self, key: str) -> str:
        raise NotImplementedError('TBI')

    def generate_keys(self):
        raise NotImplementedError('TBI')

    def get_shared_key(self) -> str:
        self._check_shared_key()
        return self._shared_key

    def _check_shared_key(self):
        if not self._shared_key or 0 == len(self._shared_key):
            raise UploadException(self._lang.shared_key_is_empty())

    @staticmethod
    def get_variant(lang: Translations, target: TargetSearch, variant: int):
        if AKey.VARIANT_VOLUME == variant:
            return SimpleVolume(lang, target)
        elif AKey.VARIANT_RANDOM == variant:
            return Random(lang, target)
        elif AKey.VARIANT_REDIS == variant:
            return Redis(lang, target)
        else:
            raise UploadException(lang.key_variant_not_set())


class SimpleVolume(AKey):
    """
     * Class Volume
     * Connect shared key and local path in format which can be used in local volume
    """

    def from_shared_key(self, key: str) -> str:
        import base64
        import binascii
        try:
            return base64.b64decode(key.encode("utf-8")).decode("utf-8").strip()
        except binascii.Error:
            raise UploadException(self._lang.shared_key_is_invalid())

    def generate_keys(self):
        import base64
        self._shared_key = base64.encodebytes(
            self._target.get_driver_location().encode('utf-8')
        ).decode("utf-8").strip()
        return self


class Random(AKey):
    """
     * Class Random
     * Connect shared key and local which has been generated by as random string
    """

    def __init__(self, lang: Translations, target: TargetSearch):
        super().__init__(lang, target)
        self._key_length = 64
        self._possibilities = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
                               'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']

    def from_shared_key(self, key: str) -> str:
        return key + TargetSearch.FILE_DRIVER_SUFF

    def generate_keys(self):
        self._shared_key = self.generate_random_text(self._key_length, self._possibilities)
        return self

    @staticmethod
    def generate_random_text(length: int, possibilities: list) -> str:
        import random
        result = ''
        size = len(possibilities)
        for pos in range(0, length):
            point = random.randint(0, size - 1)
            result += ''.join(possibilities[point:point+1])  # because slicing
        return result


class Redis(AKey):
    """
     * Class Redis
     * Connect shared key and local in format available for Redis
    """
    PREFIX = 'aupload_content_'

    def from_shared_key(self, key: str) -> str:
        return self._get_prefix() + key

    def generate_keys(self):
        import hashlib
        self._shared_key = hashlib.md5(self._target.get_final_target_name().encode("utf-8")).hexdigest()
        return self

    def _get_prefix(self) -> str:
        return self.PREFIX
