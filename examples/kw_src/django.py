import base64
import os
from django.http import HttpResponse, JsonResponse
from kw_upload.exceptions import UploadException
from kw_upload.upload import Upload
from kw_upload.responses import *
from kw_upload.drive_file import UploadData

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')

# Create your views here.


def view_initial(request):
    return HttpResponse("Demo uploader in Django")


def view_begin(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH)  # here set temp path and init everytime with it
        return JsonResponse(lib.partes_init(
            str(request.POST.get('fileName')),
            int(request.POST.get('fileSize'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(InitResponse.init_error(
            request.POST.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


def view_check(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.POST.get('sharedKey'))
        return JsonResponse(lib.partes_check(
            int(request.POST.get('segment'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(CheckResponse.init_error(
            request.POST.get('sharedKey'),
            ex
        ).get_result())


def view_part(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.POST.get('sharedKey'))
        return JsonResponse(lib.partes_upload(
            bytearray(base64.b64decode(request.POST.get('content')))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(UploadResponse.init_error(
            request.POST.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


def view_truncate(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.POST.get('sharedKey'))
        return JsonResponse(lib.partes_truncate_from(
            int(request.POST.get('segment'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(TruncateResponse.init_error(
            request.POST.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())


def view_cancel(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.POST.get('sharedKey'))
        return JsonResponse(lib.partes_cancel().get_result())
    except UploadException as ex:
        return JsonResponse(CancelResponse.init_error(
            request.POST.get('sharedKey'),
            ex
        ).get_result())


def view_done(request):
    try:
        lib = Upload(ENCODING_UPLOAD_PATH, request.POST.get('sharedKey'))
        result = lib.partes_done()
        # check uploaded content and move it on drive
        print([result.get_target_file(), result.get_file_name()])
        # answer to client
        return JsonResponse(result.get_result())
    except UploadException as ex:
        return JsonResponse(DoneResponse.init_error(
            request.POST.get('sharedKey'),
            UploadData(),
            ex
        ).get_result())
