// configs
if (jQuery && window.File && window.FileReader && window.FileList && window.Blob) {
    var langs = new UploadTranslations();
    var targetConfig = new UploadTargetConfig();
    targetConfig.targetInitPath = '//upload-file/init/';
    targetConfig.targetFilePath = '//upload-file/file/';
    targetConfig.targetDonePath = '//upload-file/done/';
    var uploadedRenderer = new UploaderRenderer(jQuery);
    var uploadedProcessor = new UploaderProcessor(jQuery, langs, targetConfig, uploadedRenderer);
    var uploadedHandler = new UploaderHandler(uploadedProcessor);
}

// runner
if (jQuery && window.File && window.FileReader && window.FileList && window.Blob) {
    // Great success! All the File APIs are supported.
    var dropZone = document.getElementById("uparea");
    dropZone.style.backgroundColor = '#a6ff80';
    // Setup the dnd listeners.
    // dropZone.addEventListener('dragover', uploadedHandler.handleDragOver, false);
    dropZone.addEventListener('drop', uploadedHandler.handleFileSelect, false);
    jQuery("#upfile").on('change', {}, uploadedHandler.handleFileInput);
}
