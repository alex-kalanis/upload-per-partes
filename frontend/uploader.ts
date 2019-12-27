class UploadTargetConfig {
    targetInitPath = "//upload-file/init/";
    targetCheckPath = "//upload-file/check/";
    targetCancelPath = "//upload-file/cancel/";
    targetTrimPath = "//upload-file/trim/";
    targetFilePath = "//upload-file/file/";
    targetDonePath = "//upload-file/done/";
}

class UploadTranslations {
    readFileCannotSlice = "Cannot slice file";
    initReturnsFollowingError = "Init returns following error: ";
    initReturnsSomethingFailed = "Init does not return a JSON data. More at console.";
    checkerReturnsSomethingFailed = "Data check does not return a JSON data. More at console.";
    dataUploadReturnsSomethingFailed = "Data upload does not return a JSON. More at console.";
    doneReturnsSomethingFailed = "Done does not return a JSON data. More at console.";
}

class UploadedFile {
    // info about file to upload

    // constants
    STATUS_STOP = 0;
    STATUS_INIT = 1;
    STATUS_RUN = 2;
    STATUS_FINISH = 3;
    STATUS_RETRY = 4;
    STATUS_DESTROY = 5;

    // initial
    /** @var {string} */
    localId = "";
    /** @var {string} */
    fileName = "";
    /** @var {number} */
    fileSize = 0;
    /** @var {File} */
    fileHandler = null;

    // processing
    /** @var {number} upload status */
    readStatus = this.STATUS_STOP;
    /** @var {string} shared key - need to remember during init and then sent repeatedly!!! */
    sharedKey = "";
    /** @var {number} total parts number */
    totalParts = 0;
    /** @var {number} last known part on both sides is... */
    lastKnownPart = 0;
    /** @var {number} last checked part on both sides is... */
    lastCheckedPart = 0;
    /** @var {number} max part size in bytes */
    partSize = 0;
    /** @var {string} when it dies... */
    errorMessage = "";

    // setters
    /**
     * @param {File} fileHandler
     */
    setInitialData(fileHandler) {
        this.localId = UploadedFile.parseLocalId(fileHandler);
        this.fileName = fileHandler.name;
        this.fileSize = parseInt(fileHandler.size);
        this.fileHandler = fileHandler;
        return this;
    }

    /**
     * @param {File} fileHandler
     * @return {string]
     */
    static parseLocalId(fileHandler) {
        return "file_" + UploadedFile.parseBase(fileHandler.name);
    }

    /**
     * @param {string} fileName
     * @return {string]
     */
    static parseBase(fileName) {
        let lastIndex = fileName.lastIndexOf('.');
        return (0 > lastIndex) ? fileName : fileName.substr(0, lastIndex);
    }

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    setInfoFromServer(serverResponse) {
        this.readStatus = this.STATUS_RUN;
        this.sharedKey = serverResponse.sharedKey;
        this.totalParts = parseInt(serverResponse.totalParts);
        this.lastKnownPart = parseInt(serverResponse.lastKnownPart);
        this.partSize = parseInt(serverResponse.partSize);
        this.errorMessage = serverResponse.errorMessage;
        return this;
    }

    /**
     * @returns {UploadedFile}
     */
    setInfoFromClearer() {
        this.readStatus = this.STATUS_RUN;
        this.lastCheckedPart = 0;
        this.errorMessage = '';
        return this;
    }

    /**
     * @param {string} message
     * @param {number} status
     * @returns {UploadedFile}
     */
    setError(message, status = this.STATUS_STOP) {
        this.readStatus = status;
        this.errorMessage = message;
        return this;
    }

    /**
     * @returns {UploadedFile}
     */
    nextFilePart() {
        this.lastKnownPart++;
        return this;
    }

    /**
     * @returns {UploadedFile}
     */
    nextCheckedPart() {
        this.lastCheckedPart++;
        return this;
    }

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    setTruncatedFromServer(serverResponse) {
        this.lastCheckedPart = this.lastKnownPart = parseInt(serverResponse.lastKnownPart);
        return this;
    }
}

class UploaderProcessor {
    // main processing class

    RESULT_OK = "OK";
    RESULT_STOP = "FAIL";

    /** @var {jQuery} */
    jQ = null;
    /** @var {UploadTargetConfig} */
    targetConfig = null;
    /** @var {UploadTranslations} */
    translations = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;
    /** @var {UploaderInit} */
    upInit = null;
    /** @var {UploaderReader} */
    upReader = null;
    /** @var {UploaderEncoder} */
    upEncoder = null;
    /** @var {UploaderChecksum} */
    upChecksum = null;
    /** @var {UploaderChecker} */
    upCheck = null;
    /** @var {UploaderRunner} */
    upRunner = null;
    /** @var {UploaderFailure} */
    upFailure = null;

    /**
     * @param {jQuery} jQ
     * @param {UploadTranslations} translations
     * @param {UploadTargetConfig} targetConfig
     * @param {UploaderRenderer} renderer
     * @param {CheckSumMD5} checksum
     */
    constructor(jQ, translations, targetConfig, renderer, checksum = null) {
        this.jQ = jQ;
        this.translations = translations;
        this.targetConfig = targetConfig;
        this.upRenderer = renderer;
        this.upEncoder = new UploaderEncoder();
        this.upChecksum = new UploaderChecksum(checksum);
        this.upReader = new UploaderReader(this);
        this.upInit = new UploadInit(this);
        this.upCheck = new UploaderChecker(this);
        this.upRunner = new UploaderRunner(this);
        this.upFailure = new UploaderFailure(this);
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    firstRead(uploadedFile) {
        this.upInit.process(uploadedFile);
    }

    /**
     * Call checking of file
     * @param {UploadedFile} uploadedFile
     */
    checkParts(uploadedFile) {
        this.upCheck.process(uploadedFile);
    }

    /**
     * Call uploading of file
     * @param {UploadedFile} uploadedFile
     */
    uploadParts(uploadedFile) {
        this.upRunner.process(uploadedFile);
    }

    /**
     * Call dumpster when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    failProcess(uploadedFile, event: any = null) {
        this.upFailure.process(uploadedFile, event);
    }

    /**
     * Call dumpster when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    failEnd(uploadedFile, event: any = null) {
        this.upFailure.end(uploadedFile, event);
    }

    getJQuery() {
        return this.jQ;
    }

    getFileReader() {
        return this.upReader;
    }

    getEncoder() {
        return this.upEncoder;
    }

    getChecksum() {
        return this.upChecksum;
    }

    getRenderer() {
        return this.upRenderer;
    }

    getLang() {
        return this.translations;
    }

    getTargetLinks() {
        return this.targetConfig;
    }
}

class UploadInit {
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} processor
     */
    constructor(processor) {
        this.upProcessor = processor;
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    process(uploadedFile) {
        let self = this;
        this.upProcessor.getJQuery().post(
            this.upProcessor.getTargetLinks().targetInitPath,
            {
                fileName: uploadedFile.fileName,
                fileSize: uploadedFile.fileSize
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    self.processServerInfo(uploadedFile, responseData);
                } else {
                    // Query dead, sent user info
                    uploadedFile.setError(self.upProcessor.getLang().initReturnsSomethingFailed);
                    self.upProcessor.getRenderer().consoleError(uploadedFile, responseData);
                }
            },
            function(err) {
                self.upProcessor.getRenderer().consoleError(uploadedFile, err);
            }
        );
    }

    /**
     * Processing driving instruction that came from server
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    processServerInfo(uploadedFile, responseData: any) {
        // is anything to upload
        if (this.upProcessor.RESULT_OK === responseData.status) {
            // start checking content
            uploadedFile.setInfoFromServer(responseData);
            this.upProcessor.getRenderer().renderReaded(uploadedFile);
            this.upProcessor.checkParts(uploadedFile);
        } else {
            // File is dead, sent user info
            uploadedFile.setError(this.upProcessor.getLang().initReturnsFollowingError + responseData.errorMessage);
            this.upProcessor.getRenderer().consoleError(uploadedFile, responseData);
        }
    }
}

class UploaderChecker {
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} processor
     */
    constructor(processor) {
        this.upProcessor = processor;
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    process(uploadedFile) {
        this.stillCheck(uploadedFile);
    }

    continueChecking(uploadedFile) {
        if (uploadedFile.STATUS_RUN == uploadedFile.readStatus) {
            this.stillCheck(uploadedFile);
        } else {
            this.upProcessor.failProcess(uploadedFile);
        }
    }

    stillCheck(uploadedFile) {
        if (uploadedFile.lastCheckedPart < uploadedFile.lastKnownPart) {
            this.checkPart(uploadedFile);
        } else {
            this.nextStep(uploadedFile);
        }
    }

    /**
     * Check data on server - for each already uploaded part
     * @param {UploadedFile} uploadedFile
     */
    checkPart(uploadedFile) {
        let self = this;
        this.upProcessor.getJQuery().post(
            this.upProcessor.getTargetLinks().targetCheckPath,
            {
                sharedKey: uploadedFile.sharedKey,
                segment: uploadedFile.lastCheckedPart
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    self.processCheckPart(uploadedFile, responseData);
                } else {
                    // Query dead, sent user info
                    uploadedFile.setError(self.upProcessor.getLang().initReturnsSomethingFailed);
                    self.upProcessor.getRenderer().consoleError(uploadedFile, responseData);
                }
            },
            function(err) {
                self.upProcessor.failProcess(uploadedFile, err);
            }
        );
    }

    nextStep(uploadedFile) {
        if (uploadedFile.STATUS_RUN == uploadedFile.readStatus) {
            this.upProcessor.uploadParts(uploadedFile);
        } else {
            this.upProcessor.failProcess(uploadedFile);
        }
    }

    /**
     * Processing check response that came from server
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    processCheckPart(uploadedFile, responseData: any) {
        if (this.upProcessor.RESULT_OK === responseData.status) {
            // got known checksum on remote - check it against local file
            let self = this;
            this.upProcessor.getFileReader().processFileRead(uploadedFile, uploadedFile.lastCheckedPart, function (result) {
                if (responseData.checksum == self.upProcessor.getChecksum().md5(result)) {
                    // this part is OK, onto the next one
                    uploadedFile.nextCheckedPart();
                    self.upProcessor.getRenderer().renderReaded(uploadedFile);
                    self.continueChecking(uploadedFile);
                } else {
                    // Check failed, time to truncate
                    self.truncatePart(uploadedFile);
                }
            }, function (event) {
                self.upProcessor.getRenderer().updateStatus(uploadedFile, event.error);
            });
        } else {
            // failed query
            this.upProcessor.failProcess(uploadedFile);
        }
    }

    /**
     * Truncate failed data on server, to upload them correctly
     * @param {UploadedFile} uploadedFile
     */
    truncatePart(uploadedFile) {
        let self = this;
        this.upProcessor.getJQuery().post(
            this.upProcessor.getTargetLinks().targetTrimPath,
            {
                sharedKey: uploadedFile.sharedKey,
                segment: uploadedFile.lastCheckedPart
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    self.processTruncatePart(uploadedFile, responseData);
                } else {
                    // Query dead, sent user info
                    uploadedFile.setError(self.upProcessor.getLang().checkerReturnsSomethingFailed);
                    self.upProcessor.getRenderer().consoleError(uploadedFile, responseData);
                }
            },
            function(err) {
                self.upProcessor.getRenderer().consoleError(uploadedFile, err);
            }
        );
    }

    /**
     * Processing truncate response that came from server
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    processTruncatePart(uploadedFile, responseData: any) {
        // is anything to upload
        if (this.upProcessor.RESULT_OK === responseData.status) {
            // Truncate came OK, time to upload the rest
            uploadedFile.setTruncatedFromServer(responseData);
            this.upProcessor.getRenderer().renderReaded(uploadedFile);
            this.nextStep(uploadedFile);
        } else {
            // Truncate failed, time say something
            this.upProcessor.failProcess(uploadedFile);
        }
    }
}

class UploaderRunner {
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} processor
     */
    constructor(processor) {
        this.upProcessor = processor;
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    process(uploadedFile) {
        this.stillRunning(uploadedFile);
    }

    stillRunning(uploadedFile) {
        if (uploadedFile.lastKnownPart < uploadedFile.totalParts) {
            this.continueRunning(uploadedFile);
        } else {
            this.closePart(uploadedFile);
        }
    }

    continueRunning(uploadedFile) {
        if (uploadedFile.STATUS_RUN == uploadedFile.readStatus) {
            this.uploadPart(uploadedFile);
        } else {
            this.upProcessor.failProcess(uploadedFile);
        }
    }

    /**
     * Sent request which contains part of uploaded file
     * @param {UploadedFile} uploadedFile
     */
    uploadPart(uploadedFile) {
        let self = this;
        this.upProcessor.getFileReader().processFileRead(uploadedFile, uploadedFile.lastKnownPart, function (result) {
            self.upProcessor.getJQuery().post(
                self.upProcessor.getTargetLinks().targetFilePath,
                {
                    sharedKey: uploadedFile.sharedKey,
                    content: self.upProcessor.getEncoder().base64(result),
                    // lastKnownPart: uploadedFile.lastKnownPart
                },
                function(responseData) {
                    if (typeof responseData == "object") {
                        self.processUploadPart(uploadedFile, responseData);
                    } else {
                        uploadedFile.setError(self.upProcessor.getLang().dataUploadReturnsSomethingFailed);
                        self.upProcessor.getRenderer().consoleError(uploadedFile, responseData);
                    }
                }
            );
        }, function (event) {
            self.upProcessor.failProcess(uploadedFile, event);
        });
    }

    /**
     * Process response about uploaded part
     * @param {uploadedFile} uploadedFile
     * @param {*} responseData
     */
    processUploadPart(uploadedFile, responseData: any) {
        this.upProcessor.getRenderer().updateBar(uploadedFile);
        this.upProcessor.getRenderer().updateStatus(uploadedFile, responseData.status);
        uploadedFile.nextCheckedPart().nextFilePart();
        this.stillRunning(uploadedFile);
    }

    /**
     * Send request about upload closure
     * @param {UploadedFile} uploadedFile
     */
    closePart(uploadedFile) {
        let self = this;
        this.upProcessor.getJQuery().post(
            this.upProcessor.getTargetLinks().targetDonePath,
            {
                sharedKey: uploadedFile.sharedKey
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    self.processCloseInfo(uploadedFile, responseData);
                } else {
                    // dead query, user info
                    uploadedFile.setError(this.getLang().doneReturnsSomethingFailed);
                    this.upRenderer.consoleError(uploadedFile, responseData);
                }
            }
        );
    }

    /**
     * Process response about file closure
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    processCloseInfo(uploadedFile, responseData: any) {
        // is anything to close
        if (this.upProcessor.RESULT_OK === responseData.status) {
            // everything ok
            uploadedFile.readStatus = uploadedFile.STATUS_FINISH;
            this.upProcessor.getRenderer().renderFinished(uploadedFile);
            this.upProcessor.getRenderer().updateStatus(uploadedFile, responseData.errorMessage);
        } else {
            // dead file, user info
            this.upProcessor.failProcess(uploadedFile);
        }
    }
}

class UploaderFailure {
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} processor
     */
    constructor(processor) {
        this.upProcessor = processor;
    }

    /**
     * Show instructions for user
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    process(uploadedFile, event: any = null) {
        // render output, want user input
        // we need info if is necessary to use total clear and if continue
        // this.stillRunning(uploadedFile);
    }

    continue(uploadedFile) {
        // init, finish, retry, destroy
        // cr-cont; end; cont; cr-end
        if ((uploadedFile.STATUS_INIT == uploadedFile.status) || (uploadedFile.STATUS_DESTROY == uploadedFile.status)) {
            this.contentRemoval(uploadedFile);
        } else {
            this.checkContinue(uploadedFile);
        }
    }

    checkContinue(uploadedFile) {
        if (uploadedFile.STATUS_INIT == uploadedFile.status) {
            this.upProcessor.firstRead(uploadedFile);
        } else if (uploadedFile.STATUS_RETRY == uploadedFile.status) {
            uploadedFile.setInfoFromClearer();
            this.upProcessor.checkPart(uploadedFile);
        } else {
            this.upProcessor.failEnd(uploadedFile);
        }
    }

    /**
     * Send request about upload closure
     * @param {UploadedFile} uploadedFile
     */
    contentRemoval(uploadedFile) {
        let self = this;
        this.upProcessor.getJQuery().post(
            this.upProcessor.getTargetLinks().targetCancelPath,
            {
                sharedKey: uploadedFile.sharedKey
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    self.processContentRemoval(uploadedFile, responseData);
                } else {
                    // dead query, user info
                    uploadedFile.setError(this.getLang().doneReturnsSomethingFailed);
                    this.upRenderer.consoleError(uploadedFile, responseData);
                }
            }
        );
    }

    /**
     * Process response about file closure
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    processContentRemoval(uploadedFile, responseData: any) {
        if (this.upProcessor.RESULT_OK === responseData.status) {
            // everything done
            this.checkContinue(uploadedFile);
        } else {
            // dead file, user info
            this.end(uploadedFile);
        }
    }

    /**
     * Show instructions, cannot be done more
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    end(uploadedFile, event: any = null) {
        // render output, no user input need
    }
}

class UploaderReader {
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} processor
     */
    constructor(processor) {
        this.upProcessor = processor;
    }

    /**
     * Processing check response that came from server
     * @param {UploadedFile} uploadedFile
     * @param {number} segment
     * @param {*} onSuccess
     * @param {*} onFailure
     */
    processFileRead(uploadedFile, segment, onSuccess: any, onFailure: any) {
        let reader = new FileReader();
        reader.onload = (event: any) => {
            if (event.target.readyState === reader.DONE) {
                // DONE == 2
                onSuccess(event.target.result);
            }
        };
        reader.onabort = (event: any) => {
            onFailure(event);
        };
        reader.onerror = (event: any) => {
            onFailure(event);
        };
        let blob = UploaderReader.fileSlice(
            uploadedFile,
            uploadedFile.partSize * segment,
            (segment + 1 === uploadedFile.totalParts) ? uploadedFile.fileSize : uploadedFile.partSize * (segment + 1)
        );
        if (null == blob) {
            uploadedFile.setError(this.upProcessor.getLang().readFileCannotSlice);
        } else {
            reader.readAsBinaryString(blob);
        }
    }

    /**
     * @param {UploadedFile} uploadedFile
     * @param {number} begin seek at beginning of segment
     * @param {number} end   seek of ending of segment
     * @returns {null|Blob}
     */
    static fileSlice(uploadedFile, begin, end) {
        if (uploadedFile.fileHandler.slice) {
            return uploadedFile.fileHandler.slice(begin, end);
        } else if (uploadedFile.fileHandler.mozSlice) {
            return uploadedFile.fileHandler.mozSlice(begin, end);
        } else if (uploadedFile.fileHandler.webkitSlice) {
            return uploadedFile.fileHandler.webkitSlice(begin, end);
        } else {
            return null;
        }
    }
}

class UploaderEncoder {

    /**
     * Encode data into Base64
     * @param {string} data
     * @return {string}
     */
    base64(data) {
        // phpjs.org
        // http://kevin.vanzonneveld.net
        // *     example 1: base64_encode('Kevin van Zonneveld');
        // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
        // mozilla has this native
        // - but breaks in 2.0.0.12!
        let b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        let o1,
            o2,
            o3,
            h1,
            h2,
            h3,
            h4,
            bits,
            i = 0,
            ac = 0,
            tmp_arr = [];
        if (!data) {
            return data;
        }
        do {
            // pack three octets into four hexets
            o1 = data.charCodeAt(i++);
            o2 = data.charCodeAt(i++);
            o3 = data.charCodeAt(i++);
            bits = (o1 << 16) | (o2 << 8) | o3;
            h1 = (bits >> 18) & 0x3f;
            h2 = (bits >> 12) & 0x3f;
            h3 = (bits >> 6) & 0x3f;
            h4 = bits & 0x3f;
            // use hexets to index into b64, and append result to encoded string
            tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
        } while (i < data.length);
        let enc = tmp_arr.join("");
        let r = data.length % 3;
        return (r ? enc.slice(0, r - 3) : enc) + "===".slice(r || 3);
    }
}

class UploaderChecksum {
    /** @var {CheckSumMD5} */
    checksum = null;

    /**
     * @param {CheckSumMD5} checksum
     */
    constructor(checksum = null) {
        this.checksum = checksum;
    }

    /**
     * Create file checksum
     * @param {Blob} file
     * @return {string}
     */
    md5 (file) {
        return (this.checksum) ? this.checksum.calcMD5(file) : '';
    }
}

class UploaderHandler {
    /** @type {UploadedFile[]} */
    uploadingFiles = []; // All uploaded files in JS
    /** @var {UploaderProcessor} */
    upProcessor = null;

    /**
     * @param {UploaderProcessor} upProcessor
     */
    constructor(upProcessor) {
        this.upProcessor = upProcessor;
    }

    handleFileSelect(evt: any) {
        // files is a FileList of File objects. List some properties.
        this.addFileInput(evt.dataTransfer.files);
    }

    handleFileInput(evt: any) {
        this.addFileInput(evt.target.files);
    }

    /**
     * @param {FileList} files
     */
    addFileInput(files) {
        for (let i = 0, f; (f = files[i]); i++) {
            let dataSource = new UploadedFile();
            dataSource.setInitialData(f);
            this.uploadingFiles.push(dataSource);
            this.upProcessor.getRenderer().renderFileItem(dataSource);
        }
    }

    /**
     * @param {string} fileId
     */
    startRead(fileId) {
        let file = this.searchFile(fileId);
        if (null != file) {
            // one
            file.readStatus = file.STATUS_INIT;
            this.upProcessor.getRenderer().process(file);
            this.upProcessor.firstRead(file);
        } else {
            // all
            for (let i = 0; i < this.uploadingFiles.length; i++) {
                this.startRead(this.uploadingFiles[i].localId);
            }
        }
    }

    /**
     * @param {string} fileId
     */
    stopRead(fileId) {
        let file = this.searchFile(fileId);
        if (null != file) {
            // one
            this.upProcessor.getRenderer().stopRead(file);
            file.readStatus = file.STATUS_STOP;
        } else {
            // all recursive
            for (let i = 0; i < this.uploadingFiles.length; i++) {
                this.stopRead(this.uploadingFiles[i].localId);
            }
        }
    }

    /**
     * @param {string} fileId
     */
    resumeRead(fileId) {
        let file = this.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_INIT;
            this.upProcessor.getRenderer().resumeRead(file);
            this.upProcessor.firstRead(file);
        } else {
            // all recursive
            for (let i = 0; i < this.uploadingFiles.length; i++) {
                this.resumeRead(this.uploadingFiles[i].localId);
            }
        }
    }

    /**
     * @param {string} fileId
     */
    abortRead(fileId) {}

    /**
     * @param {string} localId
     * @returns {null|UploadedFile}
     */
    searchFile(localId) {
        let file = null;
        for (let i = 0; i < this.uploadingFiles.length; i++) {
            if (this.uploadingFiles[i].localId === localId) {
                file = this.uploadingFiles[i];
            }
        }
        return file;
    }

    clearList() {
        for (let i = this.uploadingFiles.length; i > 0; i--) {
            this.uploadingFiles.pop();
        }
        this.upProcessor.getRenderer().clearFileSelection();
    }
}

class UploaderRenderer {

    /** @var {jQuery} */
    jQ = null;
    startTime = 0;

    /**
     * @param {jQuery} jQ
     */
    constructor(jQ) {
        this.jQ = jQ;
    }
    /**
     * @param {UploadedFile} uploadedFile
     */
    renderFileItem(uploadedFile) {
        let progress: any = this.jQ('#base_progress');
        let progress_bar: any = progress.clone(true);
        progress_bar.attr("id", uploadedFile.localId);
        let list: any = this.jQ("#list");
        list.append(progress_bar);
        let fileName: any = progress_bar.find(".filename").eq(1);
        fileName.append(uploadedFile.fileName);
        let button1: any = progress_bar.find("button").eq(1);
        button1.attr("data-key", uploadedFile.localId);
        let button2: any = progress_bar.find("button").eq(2);
        button2.attr("data-key", uploadedFile.localId);
    }

    clearFileSelection() {
        let list: any = this.jQ("#list");
        list.children().remove();
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    renderReaded(uploadedFile) {
        let node: any = this.jQ('#' + uploadedFile.localId);
        let button: any = node.find("button").eq(1);
        button.removeAttr("disabled");
        node.attr("id", uploadedFile.sharedKey);
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    renderFinished(uploadedFile) {
        this.jQ('#elapsed_time').text(UploaderRenderer.formatTime(this.getElapsedTime())); // time passed
        this.jQ('#est_time_left').text(this.calculateEstimatedTimeLeft(uploadedFile,100)); // time left
        this.jQ('#current_position').text(UploaderRenderer.calculateSize(uploadedFile.fileSize)); // last position
        this.jQ('#total_kbytes').text(UploaderRenderer.calculateSize(uploadedFile.fileSize)); // total
        this.jQ('#est_speed').text(UploaderRenderer.calculateSize(uploadedFile.fileSize)); // speed
        this.jQ('#percent_complete').text("100%"); // percents

        let node: any = this.jQ('#' + uploadedFile.localId);
        let button: any = node.find("button").eq(1);
        button.attr("disabled", "disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    consoleError(uploadedFile, responseData) {
        console.log({ uploadedFile: uploadedFile, responseData: responseData });
        this.updateStatus(uploadedFile, uploadedFile.errorMessage);
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    startRead(uploadedFile) {
        this.startTime = UploaderRenderer.getCurrentTime();
        let node: any = this.jQ('#' + uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.removeAttr("disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    stopRead(uploadedFile) {
        let node: any = this.jQ('#' + uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.attr("disabled", "disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    resumeRead(uploadedFile) {
        let node: any = this.jQ('#' + uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.removeAttr("disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    updateBar(uploadedFile) {
        let node: any = this.jQ('#' + uploadedFile.localId);
        let percent = UploaderRenderer.calculatePercent(uploadedFile);

        this.jQ('#elapsed_time').text(UploaderRenderer.formatTime(this.getElapsedTime())); // time passed
        this.jQ('#est_time_left').text(this.calculateEstimatedTimeLeft(uploadedFile, percent)); // time left
        this.jQ('#current_position').text(UploaderRenderer.calculateSize(uploadedFile.lastKnownPart * uploadedFile.partSize)); // last position
        this.jQ('#total_kbytes').text(UploaderRenderer.calculateSize(uploadedFile.fileSize)); // total
        this.jQ('#est_speed').text(UploaderRenderer.calculateSize(this.calculateSpeed(uploadedFile))); // speed
        this.jQ('#percent_complete').text(percent.toString() + "%"); // percents

        let button: any = node.find(".single").eq(1);
        button.append(percent + "%");
        button.css('width', percent + "%");
    }

    /**
     * @param {UploadedFile} uploadedFile
     * @param {string} status
     */
    updateStatus(uploadedFile, status) {
        let node: any = this.jQ('#' + uploadedFile.localId);
        let errLog: any = node.find(".errorlog").eq(1);
        errLog.append(status);
    }

    /**
     * calculate passed time
     * @param {UploadedFile} uploadedFile
     * @param {numeric} percentDone
     * @return {string}
     */
    calculateEstimatedTimeLeft (uploadedFile, percentDone) {
        let spend = this.getElapsedTime();
        if (percentDone > 0) {
            let fullTime = 100 * (spend / percentDone);
            return UploaderRenderer.formatTime(Math.abs(fullTime - spend));
        } else {
            return "N/A";
        }
    }

    /**
     * format time into something sane
     * @param {number} value int
     * @return {string}
     */
    static formatTime (value) {
        let hrs = Math.floor(value / 3600);
        let min = Math.floor(value / 60) - hrs * 60;
        let sec = Math.floor(value % 60);
        return UploaderRenderer.pad(hrs, 2) + ":" + UploaderRenderer.pad(min, 2) + ":" + UploaderRenderer.pad(sec, 2);
    }

    /**
     * @param {numeric} number
     * @param {numeric} length
     * @return {string}
     */
    static pad (number, length) {
        let str = "" + number;
        while (str.length < length) {
            str = "0" + str;
        }
        return str;
    }

    /**
     * calculate percents
     * @param {UploadedFile} uploadedFile
     * @return {numeric}
     */
    static calculatePercent (uploadedFile) {
        let percent = Math.round((uploadedFile.lastKnownPart) / uploadedFile.totalParts);
        percent = !percent ? 0 : percent * 100;
        return percent;
    }

    /**
     * calculate processing speed
     * @param {numeric} bytesProcessed int
     * @return {numeric}
     */
    calculateSpeed (bytesProcessed) {
        let elapsedTime = this.getElapsedTime();

        if (elapsedTime < 1) {
            return 0;
        }
        if (bytesProcessed == 0) {
            return 0;
        }
        return bytesProcessed / elapsedTime;
    }

    /**
     * calculate sizes
     * @param bytesProcessed int
     * @return string
     */
    static calculateSize (bytesProcessed) {
        let sizes = ["Bytes", "KB", "MB", "GB", "TB"];

        if (0 == bytesProcessed){
            return "0 Byte";
        }

        let i = Math.floor(Math.log(bytesProcessed) / Math.log(1024));
        return (Math.round((bytesProcessed / Math.pow(1024, i)) * 100) / 100).toFixed(2) + " " + sizes[i];
    }

    /**
     * amount of passed seconds
     * @return int
     */
    getElapsedTime () {
        return UploaderRenderer.getCurrentTime() - this.startTime;
    }

    /**
     * current time for init and calculations
     * @return int
     */
    static getCurrentTime () {
        return new Date().getTime() / 1000;
    }
}
