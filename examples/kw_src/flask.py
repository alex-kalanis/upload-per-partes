import base64
import os
from flask import Flask, jsonify, request
from kw_upload.exceptions import UploadException
from kw_upload.responses import *
from kw_upload.upload import Uploader
from kw_upload.info_format import DataPack

app = Flask(__name__)

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')


@app.route('/')
def instant():
    return "Demo uploader in Flask"


@app.route('/uploader/begin', methods=['POST'])
def begin():
    try:
        lib = Uploader()  # here set temp path and init everytime with it
        return jsonify(lib.init(
            ENCODING_UPLOAD_PATH,
            str(request.form.get('fileName')),
            int(request.form.get('fileSize'))
        ).get_result())
    except UploadException as ex:
        return jsonify(InitResponse.init_error(
            DataPack(),
            ex
        ).get_result())


@app.route('/uploader/check', methods=['POST'])
def check():
    try:
        lib = Uploader()
        return jsonify(lib.check(
            str(request.form.get('sharedKey')),
            int(request.form.get('segment'))
        ).get_result())
    except UploadException as ex:
        return jsonify(CheckResponse.init_error(
            str(request.form.get('sharedKey')),
            ex
        ).get_result())


@app.route('/uploader/part', methods=['POST'])
def part():
    try:
        lib = Uploader()
        return jsonify(lib.upload(
            str(request.form.get('sharedKey')),
            bytes(base64.b64decode(request.form.get('content')))
        ).get_result())
    except UploadException as ex:
        return jsonify(UploadResponse.init_error(
            str(request.form.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())


@app.route('/uploader/truncate', methods=['POST'])
def truncate():
    try:
        lib = Uploader()
        return jsonify(lib.truncate_from(
            str(request.form.get('sharedKey')),
            int(request.form.get('segment'))
        ).get_result())
    except UploadException as ex:
        return jsonify(TruncateResponse.init_error(
            str(request.form.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())


@app.route('/uploader/cancel', methods=['POST'])
def cancel():
    try:
        lib = Uploader()
        return jsonify(lib.cancel(
            str(request.form.get('sharedKey'))
        ).get_result())
    except UploadException as ex:
        return jsonify(CancelResponse.init_error(
            str(request.form.get('sharedKey')),
            ex
        ).get_result())


@app.route('/uploader/done', methods=['POST'])
def done():
    try:
        lib = Uploader()
        result = lib.done(
            str(request.form.get('sharedKey'))
        )
        # check uploaded content and move it on drive
        print([result.get_temporary_location(), result.get_file_name()])
        # answer to client
        return jsonify(result.get_result())
    except UploadException as ex:
        return jsonify(DoneResponse.init_error(
            str(request.form.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())


if __name__ == '__main__':
    app.run()
