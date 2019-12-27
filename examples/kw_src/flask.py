import base64
import os
from flask import Flask, jsonify, request
from kw_upload.exceptions import UploadException
from kw_upload.upload import Upload
from kw_upload.responses import *
from kw_upload.drive_file import UploadData

app = Flask(__name__)

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')


@app.route('/')
def instant():
    return "Demo uploader in Flask"


@app.route('/uploader/begin', methods=['POST'])
def begin():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH)  # here set temp path and init everytime with it
        return jsonify(lib.partes_init(
            str(request.form.get('fileName')),
            int(request.form.get('fileSize'))
        ).get_result())
    except UploadException as ex:
        return jsonify(InitResponse.init_error(
            request.form.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


@app.route('/uploader/check', methods=['POST'])
def check():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.form.get('sharedKey'))
        return jsonify(lib.partes_check(
            int(request.form.get('segment'))
        ).get_result())
    except UploadException as ex:
        return jsonify(CheckResponse.init_error(
            request.form.get('sharedKey'),
            ex
        ).get_result())


@app.route('/uploader/part', methods=['POST'])
def part():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.form.get('sharedKey'))
        return jsonify(lib.partes_upload(
            bytearray(base64.b64decode(request.form.get('content')))
        ).get_result())
    except UploadException as ex:
        return jsonify(UploadResponse.init_error(
            request.form.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


@app.route('/uploader/truncate', methods=['POST'])
def truncate():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.form.get('sharedKey'))
        return jsonify(lib.partes_truncate_from(
            int(request.form.get('segment'))
        ).get_result())
    except UploadException as ex:
        return jsonify(TruncateResponse.init_error(
            request.form.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


@app.route('/uploader/cancel', methods=['POST'])
def cancel():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.form.get('sharedKey'))
        return jsonify(lib.partes_cancel().get_result())
    except UploadException as ex:
        return jsonify(CancelResponse.init_error(
            request.form.get('sharedKey'),
            ex
        ).get_result())


@app.route('/uploader/done', methods=['POST'])
def done():
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.form.get('sharedKey'))
        result = lib.partes_done()
        # check uploaded content and move it on drive
        print([result.get_target_file(), result.get_file_name()])
        # answer to client
        return jsonify(result.get_result())
    except UploadException as ex:
        return jsonify(DoneResponse.init_error(
            request.form.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


if __name__ == '__main__':
    app.run()
