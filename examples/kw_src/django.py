import base64
import os
from django.http import HttpResponse, JsonResponse
from kw_upload.exceptions import UploadException
from kw_upload.responses import *
from kw_upload.info_format import DataPack
from kw_upload.upload import Uploader

ENCODING_UPLOAD_PATH = os.path.realpath('some_temp_dir_for_save_file')

# Create your views here.


def view_initial(request):
    return HttpResponse("Demo uploader in Django")


def view_begin(request):
    try:
        lib = Uploader()  # here set temp path and init everytime with it
        return JsonResponse(lib.init(
            ENCODING_UPLOAD_PATH,
            str(request.POST.get('fileName')),
            int(request.POST.get('fileSize'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(InitResponse.init_error(
            DataPack(),
            ex
        ).get_result())


def view_check(request):
    try:
        lib = Uploader()
        return JsonResponse(lib.check(
            str(request.POST.get('sharedKey')),
            int(request.POST.get('segment'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(CheckResponse.init_error(
            str(request.POST.get('sharedKey')),
            ex
        ).get_result())


def view_part(request):
    try:
        lib = Uploader()
        return JsonResponse(lib.upload(
            str(request.POST.get('sharedKey')),
            bytes(base64.b64decode(request.POST.get('content')))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(UploadResponse.init_error(
            str(request.POST.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())


def view_truncate(request):
    try:
        lib = Uploader()
        return JsonResponse(lib.truncate_from(
            str(request.POST.get('sharedKey')),
            int(request.POST.get('segment'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(TruncateResponse.init_error(
            str(request.POST.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())


def view_cancel(request):
    try:
        lib = Uploader()
        return JsonResponse(lib.cancel(
            str(request.POST.get('sharedKey'))
        ).get_result())
    except UploadException as ex:
        return JsonResponse(CancelResponse.init_error(
            str(request.POST.get('sharedKey')),
            ex
        ).get_result())


def view_done(request):
    try:
        lib = Uploader()
        result = lib.done(
            str(request.POST.get('sharedKey'))
        )
        # check uploaded content and move it on drive
        print([result.get_temporary_location(), result.get_file_name()])
        # answer to client
        return JsonResponse(result.get_result())
    except UploadException as ex:
        return JsonResponse(DoneResponse.init_error(
            str(request.POST.get('sharedKey')),
            DataPack(),
            ex
        ).get_result())
