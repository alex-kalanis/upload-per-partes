import base64
import os
import bottle

from kw_upload.exceptions import UploadException
from kw_upload.responses import *
from kw_upload.info_format import DataPack
from kw_upload.upload import Uploader

app = bottle.Bottle()

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')


@app.get('/')
def instant():
    return "Demo uploader in (Green) Bottle"


@app.post('/uploader/begin')
def begin():
    try:
        lib = Uploader()  # here set temp path and init everytime with it
        return lib.init(
            ENCODING_UPLOAD_PATH,
            str(bottle.request.forms.get('fileName')),
            int(bottle.request.forms.get('fileSize'))
        ).get_result()
    except UploadException as ex:
        return InitResponse.init_error(
            DataPack(),
            ex
        ).get_result()


@app.post('/uploader/check')
def check():
    try:
        lib = Uploader()
        return lib.check(
            str(bottle.request.forms.get('sharedKey')),
            int(bottle.request.forms.get('segment'))
        ).get_result()
    except UploadException as ex:
        return CheckResponse.init_error(
            str(bottle.request.forms.get('sharedKey')),
            ex
        ).get_result()


@app.post('/uploader/part')
def part():
    try:
        lib = Uploader()
        return lib.upload(
            str(bottle.request.forms.get('sharedKey')),
            bytes(base64.b64decode(bottle.request.forms.get('content')))
        ).get_result()
    except UploadException as ex:
        return UploadResponse.init_error(
            str(bottle.request.forms.get('sharedKey')),
            DataPack(),
            ex
        ).get_result()


@app.post('/uploader/truncate')
def truncate():
    try:
        lib = Uploader()
        return lib.truncate_from(
            str(bottle.request.forms.get('sharedKey')),
            int(bottle.request.forms.get('segment'))
        ).get_result()
    except UploadException as ex:
        return TruncateResponse.init_error(
            str(bottle.request.forms.get('sharedKey')),
            DataPack(),
            ex
        ).get_result()


@app.post('/uploader/cancel')
def cancel():
    try:
        lib = Uploader()
        return lib.cancel(
            str(bottle.request.forms.get('sharedKey'))
        ).get_result()
    except UploadException as ex:
        return CancelResponse.init_error(
            str(bottle.request.forms.get('sharedKey')),
            ex
        ).get_result()


@app.post('/uploader/done')
def done():
    try:
        lib = Uploader()
        result = lib.done(
            str(bottle.request.forms.get('sharedKey'))
        )
        # check uploaded content and move it on drive
        print([result.get_temporary_location(), result.get_file_name()])
        # answer to client
        return result.get_result()
    except UploadException as ex:
        return DoneResponse.init_error(
            str(bottle.request.forms.get('sharedKey')),
            DataPack(),
            ex
        ).get_result()


if __name__ == '__main__':
    bottle.run(app, host="", port=8000)
