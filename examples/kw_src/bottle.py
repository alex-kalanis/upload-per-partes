import base64
import os
import bottle

from kw_upload.exceptions import UploadException
from kw_upload.upload import Upload
from kw_upload.responses import *
from kw_upload.drive_file import UploadData

app = bottle.Bottle()

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')


@app.get('/')
def instant():
    return "Demo uploader in (Green) Bottle"


@app.post('/uploader/begin')
def begin():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH)  # here set temp path and init everytime with it
        return lib.partes_init(
            str(bottle.request.forms.get('fileName')),
            int(bottle.request.forms.get('fileSize'))
        ).get_result()
    except UploadException as ex:
        return InitResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            UploadData(),
            ex
        ).get_result()


@app.post('/uploader/check')
def check():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, bottle.request.forms.get('sharedKey'))
        return lib.partes_check(
            int(bottle.request.forms.get('segment'))
        ).get_result()
    except UploadException as ex:
        return CheckResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            ex
        ).get_result()


@app.post('/uploader/part')
def part():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, bottle.request.forms.get('sharedKey'))
        return lib.partes_upload(
            bytearray(base64.b64decode(bottle.request.forms.get('content')))
        ).get_result()
    except UploadException as ex:
        return UploadResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            UploadData(),
            ex
        ).get_result()


@app.post('/uploader/truncate')
def truncate():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, bottle.request.forms.get('sharedKey'))
        return lib.partes_truncate_from(
            int(bottle.request.forms.get('segment'))
        ).get_result()
    except UploadException as ex:
        return TruncateResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            UploadData(),
            ex
        ).get_result()


@app.post('/uploader/cancel')
def cancel():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, bottle.request.forms.get('sharedKey'))
        return lib.partes_cancel().get_result()
    except UploadException as ex:
        return CancelResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            ex
        ).get_result()


@app.post('/uploader/done')
def done():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, bottle.request.forms.get('sharedKey'))
        result = lib.partes_done()
        # check uploaded content and move it on drive
        print([result.get_target_file(), result.get_file_name()])
        # answer to client
        return result.get_result()
    except UploadException as ex:
        return DoneResponse.init_error(
            bottle.request.forms.get('sharedKey'),
            UploadData(),
            ex
        ).get_result()


if __name__ == '__main__':
    bottle.run(app, host="", port=8000)
