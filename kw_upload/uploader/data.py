class DataPack:

    @staticmethod
    def init():
        return DataPack()

    def __init__(self):
        self.file_name = ''
        self.temp_location = ''
        self.file_size = 0
        self.parts_count = 0
        self.bytes_per_part = 0
        self.last_known_part = 0

    def set_data(self,
                 file_name: str,
                 temp_location: str,
                 file_size: int,
                 parts_count: int = 0,
                 bytes_per_part: int = 0,
                 last_known_part: int = 0
                 ):
        self.file_name = file_name
        self.temp_location = temp_location
        self.file_size = file_size
        self.parts_count = parts_count
        self.bytes_per_part = bytes_per_part
        self.last_known_part = last_known_part
        return self

    def sanitize_data(self):
        self.file_name = str(self.file_name)
        self.temp_location = str(self.temp_location)
        self.file_size = int(self.file_size)
        self.parts_count = int(self.parts_count)
        self.bytes_per_part = int(self.bytes_per_part)
        self.last_known_part = int(self.last_known_part)
        return self
