document.addEventListener('DOMContentLoaded', function () {
    // configs
    if (jQuery && UploaderReader.canRead(window)) {
        var langs = new UploadTranslations();
        var targetConfig = new UploadTargetConfig();
        targetConfig.targetInitPath = '//upload-file/init/';
        targetConfig.targetFilePath = '//upload-file/file/';
        targetConfig.targetDonePath = '//upload-file/done/';
        var uploaderQuery = new UploaderQuery();
        var uploadedProcessor = new UploaderProcessor();
        uploadedProcessor.init(uploaderQuery.init(jQuery), langs, targetConfig);
    }
    
    // runner
    if (jQuery && UploaderReader.canRead(window)) {
        // Great success! All the File APIs are supported.
        var dropZone = document.getElementById("uparea");
        dropZone.style.backgroundColor = '#a6ff80';
        // Setup the dnd listeners.
        // dropZone.addEventListener('dragover', uploadedProcessor.getHandler().handleDragOver, false);
        dropZone.addEventListener('drop', uploadedProcessor.getHandler().handleFileSelect, false);
        jQuery("#upfile").on('change', {}, uploadedProcessor.getHandler().handleFileInput);
    }
});