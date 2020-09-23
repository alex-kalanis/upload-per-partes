from kw_tests.common_class import CommonTestClass
from kw_upload.uploader.essentials import TargetSearch
from kw_upload.exceptions import UploadException
from kw_upload.keys import AKey, SimpleVolume, Random, Redis
from kw_upload.uploader.translations import Translations


class KeysTest(CommonTestClass):

    def test_init(self):
        lang = Translations()
        target = TargetSearch(lang)
        assert isinstance(AKey.get_variant(lang, target, AKey.VARIANT_VOLUME), SimpleVolume)
        assert isinstance(AKey.get_variant(lang, target, AKey.VARIANT_RANDOM), Random)
        assert isinstance(AKey.get_variant(lang, target, AKey.VARIANT_REDIS), Redis)

    def test_init_fail(self):
        lang = Translations()
        target = TargetSearch(lang)
        try:
            AKey.get_variant(lang, target, 0)
            assert False, 'Use unknown variant'
        except UploadException as ex:
            assert 'KEY VARIANT NOT SET' == ex.get_message()

    def test_shared_fail(self):
        lang = Translations()
        lib = Random(lang, TargetSearch(lang))
        try:
            lib.get_shared_key()  # no key set!
            assert False, 'Got empty shared key'
        except UploadException as ex:
            assert 'SHARED KEY IS EMPTY' == ex.get_message()

    def test_random(self):
        assert 'aaaaaaa' == Random.generate_random_text(7, ['a','a','a','a'])

        lang = Translations()
        lib = Random(lang, TargetSearch(lang))
        assert 'abcdefghi' + TargetSearch.FILE_DRIVER_SUFF == lib.from_shared_key('abcdefghi')

    def test_redis(self):
        import hashlib
        lang = Translations()
        target = TargetSearch(lang)
        target.set_remote_file_name('poiuztrewq').set_target_dir(self._get_test_dir()).process()
        lib = Redis(lang, target)
        lib.generate_keys()

        key1 = 'poiuztrewq'
        key2 = '/tmp/lkjhg'
        assert hashlib.md5(key1.encode("utf-8")).hexdigest() == lib.get_shared_key()
        assert Redis.PREFIX + key2 == lib.from_shared_key(key2)

    def test_volume(self):
        import base64
        lang = Translations()
        target = TargetSearch(lang)
        target.set_remote_file_name('poiuztrewq').set_target_dir('/tmp/').process()
        lib = SimpleVolume(lang, target)
        lib.generate_keys()

        key1 = '/tmp/poiuztrewq' + TargetSearch.FILE_DRIVER_SUFF
        key2 = '/tmp/lkjhg'
        assert base64.encodebytes(key1.encode("utf-8")).decode("utf-8").strip() == lib.get_shared_key()
        assert key2 == lib.from_shared_key(base64.encodebytes(key2.encode("utf-8")).decode("utf-8").strip())
        try:
            lib.from_shared_key('**' + key2)  # aaand failed... - chars outside the b64
            assert False, 'Decode some unknown chars'
        except UploadException as ex:
            assert 'SHARED KEY IS INVALID' == ex.get_message()
