from .uploader.data import DataPack


class IInfoFormatting:
    """
     * Class IInfoFormatting
     * Drive file format - abstract for each variant
    """

    def from_format(self, content: str) -> DataPack:
        """
        :param content:
        :return DataPack:
        :raise UploadException:
        """
        raise NotImplementedError('TBI')

    def to_format(self, data: DataPack) -> str:
        """
        :param data:
        :return str:
        :raise UploadException:
        """
        raise NotImplementedError('TBI')


class IInfoStorage:
    """
     * Class IInfoStorage
     * Target storage for data stream
    """

    def exists(self, key: str) -> bool:
        raise NotImplementedError('TBI')

    def load(self, key: str) -> str:
        raise NotImplementedError('TBI')

    def save(self, key: str, data: str):
        raise NotImplementedError('TBI')

    def remove(self, key: str):
        raise NotImplementedError('TBI')


class IDataStorage:
    """
     * Class IDataStorage
     * Target storage for data stream
    """

    def exists(self, location: str) -> bool:
        """
         * If that file exists
        :param location: string
        :return bool:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')

    def add_part(self, location: str, content: bytes, seek: int = None):
        """
         * Add part to file
        :param location: string
        :param content:
        :param seek:
        :return void:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')

    def get_part(self, location: str, offset: int, limit: int = None) -> bytes:
        """
         * Get part of file
        :param location:
        :param offset:
        :param limit:
        :return void:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')

    def truncate(self, location: str, offset: int):
        """
         * Truncate data file
        :param location:
        :param offset:
        :return void:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')

    def remove(self, location: str):
        """
         * Remove whole data file
        :param location:
        :return void:
        :raise UploadException:
        """
        raise NotImplementedError('TBA')
