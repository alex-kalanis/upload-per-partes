
class Translations:
    """
     * Return translated quotes from backend
     * - necessary due many translation systems through web
     * For work extends this class and pass that extension into your project
    """
    @staticmethod
    def init():
        return Translations()

    def sent_name_is_empty(self) -> str:
        return 'SENT FILE NAME IS EMPTY'

    def upload_name_is_empty(self) -> str:
        return 'UPLOAD FILE NAME IS EMPTY'

    def shared_key_is_empty(self) -> str:
        return 'SHARED KEY IS EMPTY'

    def shared_key_is_invalid(self) -> str:
        return 'SHARED KEY IS INVALID'

    def key_variant_not_set(self) -> str:
        return 'KEY VARIANT NOT SET'

    def target_dir_is_empty(self) -> str:
        return 'TARGET DIR IS NOT SET'

    def drive_file_already_exists(self) -> str:
        return 'DRIVEFILE ALREADY EXISTS'

    def drive_file_not_continuous(self) -> str:
        return 'DRIVEFILE IS NOT CONTINUOUS'

    def drive_file_cannot_remove(self) -> str:
        return 'DRIVEFILE CANNOT BE REMOVED'

    def drive_file_variant_not_set(self) -> str:
        return 'DRIVEFILE VARIANT NOT SET'

    def drive_file_cannot_read(self) -> str:
        return 'CANNOT READ DRIVEFILE'

    def drive_file_cannot_write(self) -> str:
        return 'CANNOT WRITE DRIVEFILE'

    def cannot_remove_data(self) -> str:
        return 'CANNOT REMOVE DATA'

    def read_too_early(self) -> str:
        return 'READ TOO EARLY'

    def cannot_open_file(self) -> str:
        return 'CANNOT OPEN FILE'

    def cannot_read_file(self) -> str:
        return 'CANNOT READ FILE'

    def cannot_seek_file(self) -> str:
        return 'CANNOT SEEK FILE'

    def cannot_write_file(self) -> str:
        return 'CANNOT WRITE FILE'

    def cannot_truncate_file(self) -> str:
        return 'FILE CANNOT TRUNCATE'

    def segment_out_of_bounds(self) -> str:
        return 'SEGMENT OUT OF BOUNDS'

    def segment_not_uploaded_yet(self) -> str:
        return 'SEGMENT NOT UPLOADED YET'
