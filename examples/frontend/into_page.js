document.addEventListener('DOMContentLoaded', function () {
    // configs
    if (jQuery && uploaderReader.canRead(window)) {
        var langs = new UploadTranslations();
        var targetConfig = new UploadTargetConfig();
        targetConfig.targetInitPath = '//upload-file/init/';
        targetConfig.targetFilePath = '//upload-file/file/';
        targetConfig.targetDonePath = '//upload-file/done/';
        uploaderProcessor.init(uploaderQuery.init(jQuery), langs, targetConfig);
    }
    
    // runner
    if (jQuery && uploaderReader.canRead(window)) {
        // Great success! All the File APIs are supported.
        var dropZone = document.getElementById("uparea");
        dropZone.style.backgroundColor = '#a6ff80';
        // Setup the dnd listeners.
        // dropZone.addEventListener('dragover', uploadedProcessor.getHandler().handleDragOver, false);
        dropZone.addEventListener('drop', uploaderProcessor.getHandler().handleFileSelect, false);
        jQuery("#upfile").on('change', {}, uploaderProcessor.getHandler().handleFileInput);
    }
});
