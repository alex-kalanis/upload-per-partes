
class Translations:
    """
     * Return translated quotes from backend
     * - necessary due many translation systems through web
     * For work extends this class and pass that extension into your project
    """
    def upp_sent_name_is_empty(self) -> str:
        return 'SENT FILE NAME IS EMPTY'

    def upp_upload_name_is_empty(self) -> str:
        return 'UPLOAD FILE NAME IS EMPTY'

    def upp_shared_key_is_empty(self) -> str:
        return 'SHARED KEY IS EMPTY'

    def upp_shared_key_is_invalid(self) -> str:
        return 'SHARED KEY IS INVALID'

    def upp_key_variant_not_set(self) -> str:
        return 'KEY VARIANT NOT SET'

    def upp_key_variant_is_wrong(self, class_name: str) -> str:
        return 'KEY VARIANT IS WRONG'

    def upp_target_dir_is_empty(self) -> str:
        return 'TARGET DIR IS NOT SET'

    def upp_drive_file_already_exists(self, drive_file: str) -> str:
        return 'DRIVEFILE ALREADY EXISTS'

    def upp_drive_file_not_continuous(self, drive_file: str) -> str:
        return 'DRIVEFILE IS NOT CONTINUOUS'

    def upp_drive_file_cannot_remove(self, key: str) -> str:
        return 'DRIVEFILE CANNOT BE REMOVED'

    def upp_drive_file_variant_not_set(self) -> str:
        return 'DRIVEFILE VARIANT NOT SET'

    def upp_drive_file_variant_is_wrong(self, class_name: str) -> str:
        return 'DRIVEFILE VARIANT IS WRONG'

    def upp_drive_file_cannot_read(self, key: str) -> str:
        return 'CANNOT READ DRIVEFILE'

    def upp_drive_file_cannot_write(self, key: str) -> str:
        return 'CANNOT WRITE DRIVEFILE'

    def upp_cannot_remove_data(self, location: str) -> str:
        return 'CANNOT REMOVE DATA'

    def upp_read_too_early(self, key: str) -> str:
        return 'READ TOO EARLY'

    def upp_cannot_open_file(self, location: str) -> str:
        return 'CANNOT OPEN FILE'

    def upp_cannot_read_file(self, location: str) -> str:
        return 'CANNOT READ FILE'

    def upp_cannot_seek_file(self, location: str) -> str:
        return 'CANNOT SEEK FILE'

    def upp_cannot_write_file(self, location: str) -> str:
        return 'CANNOT WRITE FILE'

    def upp_cannot_truncate_file(self, location: str) -> str:
        return 'FILE CANNOT TRUNCATE'

    def upp_segment_out_of_bounds(self, segment: int) -> str:
        return 'SEGMENT OUT OF BOUNDS'

    def upp_segment_not_uploaded_yet(self, segment: int) -> str:
        return 'SEGMENT NOT UPLOADED YET'
