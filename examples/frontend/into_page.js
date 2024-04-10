/** rewrite renderer */
uploaderRenderer.renderFileItem = function(uploadedFile) {
    var progressBasicBox = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.baseProgress);
    var progressBox = progressBasicBox.clone(true);
    progressBox.attr(uploaderRenderer.upIdent.localId, uploadedFile.localId);
    progressBox.removeAttr(uploaderRenderer.upIdent.baseProgress);
    var list = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.knownBulk);
    list.append(progressBox);
    var fileName = progressBox.find(".filename").first();
    fileName.append(uploadedFile.fileName);
    var buttons_retry = progressBox.find("button.button_retry").first();
    buttons_retry[0].onclick = function() {
        uploaderProcessor.getHandler().retryRead(uploadedFile.localId);
    };
    var buttons_resume = progressBox.find("button.button_resume").first();
    buttons_resume[0].onclick = function() {
        uploaderProcessor.getHandler().resumeRead(uploadedFile.localId);
    };
    var buttons_stop = progressBox.find("button.button_stop").first();
    buttons_stop[0].onclick = function() {
        uploaderProcessor.getHandler().stopRead(uploadedFile.localId);
    };
};
uploaderRenderer.renderReaded = function(uploadedFile) {
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var button = node.find("button.button_retry").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.renderFinished = function(uploadedFile) {
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.elapsedTime, uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedTimeLeft, uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, 100));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.currentPosition, uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.totalSize, uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedSpeed, uploaderRenderer.calculateSize(0));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.percentsComplete, "100%");

    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var button = node.find("button").first();
    button[0].setAttribute("disabled", "disabled");
};
uploaderRenderer.startRead = function(uploadedFile) {
    uploaderRenderer.startTime = uploaderRenderer.getCurrentTime();
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var button = node.find("button.button_resume").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.stopRead = function(uploadedFile) {
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var button = node.find("button.button_resume").first();
    button[0].setAttribute("disabled", "disabled");
};
uploaderRenderer.resumeRead = function(uploadedFile) {
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var button = node.find("button.button_resume").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.updateBar = function(uploadedFile) {
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var checkedPercent = uploaderRenderer.calculateCheckedPercent(uploadedFile);
    var uploadPercent = uploaderRenderer.calculatePercent(uploadedFile);

    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .elapsed_time", uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .est_time_left", uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, uploadPercent));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .current_position", uploaderRenderer.calculateSize(uploadedFile.lastKnownPart * uploadedFile.partSize));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .total_kbytes", uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .est_speed", uploaderRenderer.calculateSize(uploaderRenderer.calculateSpeed(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .percent_complete", uploadPercent.toString() + "%");

    var percentDone = node.find(".progressbar-wrapper .uploaded").first();
    percentDone[0].style.paddingLeft = (uploadPercent - checkedPercent).toString() + "%";

    var percentChecked = node.find(".progressbar-wrapper .checked").first();
    percentChecked[0].style.paddingLeft = checkedPercent.toString() + "%";
};
uploaderRenderer.updateStatus = function(uploadedFile, status) {
    if (status === undefined) {
        status = uploadedFile.errorMessage;
    }
    if (status == null) {
        status = uploadedFile.errorMessage;
    }
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var errLog = node.find("." + uploaderRenderer.upIdent.errorLog);
    errLog[0].append(status);
};
uploaderFailure.process = function(uploadedFile, event) {
    if (event === undefined) {
        event = uploadedFile.errorMessage;
    }
    if (event == null) {
        event = uploadedFile.errorMessage;
    }
    var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    var errLog = node.find("." + uploaderRenderer.upIdent.errorLog);
    errLog[0].append(event);
};

document.addEventListener('DOMContentLoaded', function () {
    // configs
    if ($ && uploaderReader.canRead(window)) {
        var lang = new UploadTranslations();
        uploaderProcessor.init(uploaderQuery.init($), lang, targetConfig, checkSumMD5);
    }

    // runner
    if ($ && uploaderReader.canRead(window)) {
        // Success. All the File APIs are supported. And JQuery is here too
        // Add autostart to action handler
        uploaderProcessor.getHandler().handleFileSelection = function (event) {
            uploaderProcessor.getHandler().handleFileSelect(event);
            uploaderProcessor.getHandler().startRead();
        };
        uploaderProcessor.getHandler().handleFileInputs = function (event) {
            uploaderProcessor.getHandler().handleFileInput(event);
            uploaderProcessor.getHandler().startRead();
        };
        // drop zone
        var dropZone = document.getElementById("uparea");
        dropZone.className = 'can_upload';
        // Setup the dnd listeners.
        dropZone.addEventListener('drop', uploaderProcessor.getHandler().handleFileSelection, false);
        dropZone.addEventListener('click', function () {
            // classical input - on click ->> https://stackoverflow.com/questions/16215771/how-to-open-select-file-dialog-via-js
            var dummyInput = document.createElement('input');
            dummyInput.type = 'file';
            dummyInput.onchange = uploaderProcessor.getHandler().handleFileInputs;
            dummyInput.click();
        });

        var buttons_area = $(".upload_buttons").first();
        buttons_area[0].style.visibility = 'visible';
        var buttons_abort = $("button.button_abort").first();
        buttons_abort[0].onclick = function() {
            uploaderProcessor.getHandler().abortRead();
        };
        var buttons_clear = $("button.button_clear").first();
        buttons_clear[0].onclick = function() {
            uploaderProcessor.getHandler().clearList();
        };
    }
});
