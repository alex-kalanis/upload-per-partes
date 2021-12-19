/**
 * Targets paths on actual site
 */
class UploadTargetConfig {
    targetInitPath = "//upload-file/init/";
    targetCheckPath = "//upload-file/check/";
    targetCancelPath = "//upload-file/cancel/";
    targetTrimPath = "//upload-file/trim/";
    targetFilePath = "//upload-file/file/";
    targetDonePath = "//upload-file/done/";
}

/**
 * Translations for uploader
 */
class UploadTranslations {
    readFileCannotSlice = "Cannot slice file";
    initReturnsFollowingError = "Init returns following error: ";
    initReturnsSomethingFailed = "Init does not return a JSON data. More at console.";
    checkerReturnsSomethingFailed = "Data check does not return a JSON data. More at console.";
    dataUploadReturnsSomethingFailed = "Data upload does not return a JSON. More at console.";
    doneReturnsSomethingFailed = "Done does not return a JSON data. More at console.";
}

/**
 * Identify any target in selection box
 * Overwrite with values in your own selection box
 */
class UploadIdentification {
    baseProgress = 'base_progress';
    knownBulk = 'list';
    elapsedTime = 'elapsed_time'; // time passed
    estimatedTimeLeft = 'est_time_left'; // time left
    currentPosition = 'current_position'; // last position
    totalSize = 'total_kbytes'; // total
    estimatedSpeed = 'est_speed'; // speed
    percentsComplete = 'percent_complete'; // percents
    progressCounter = 'single';
    localId = 'id';
    dataKey = 'data-key'; // attribute to pass key between displayed and stored data
    errorLog = 'errorlog';
}

/**
 * Client-side info about file to upload
 */
class UploadedFile {
    // constants
    STATUS_STOP = 0;
    STATUS_INIT = 1;
    STATUS_RUN = 2;
    STATUS_FINISH = 3;
    STATUS_RETRY = 4;
    STATUS_DESTROY = 5;

    RESULT_OK = "OK";
    RESULT_FAIL = "FAIL";

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
    /** @var {number} when the upload starts */
    startTime = 0;

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
     * @return {string}
     */
    static parseLocalId(fileHandler) {
        return "file_" + UploadedFile.parseBase(fileHandler.name);
    }

    /**
     * @param {string} fileName
     * @return {string}
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

/**
 * Main processing class
 * Used for passing info between steps, so they do not need to know about each other
 */
class UploaderProcessor {

    /** @var {UploaderInit} */
    upInit = null;
    /** @var {UploaderChecker} */
    upCheck = null;
    /** @var {UploaderRunner} */
    upRunner = null;
    /** @var {UploaderFailure} */
    upFailure = null;
    /** @var {UploaderHandler} */
    upHandler = null;

    /**
     * @param {UploaderQuery} upQuery
     * @param {UploadTranslations} translations
     * @param {UploadTargetConfig} targetConfig
     * @param {CheckSumMD5} checksum
     */
    constructor(upQuery, translations, targetConfig, checksum = null) {
        let remoteQuery = new UploaderRemoteQuery(upQuery, targetConfig);
        let upRenderer = new UploaderRenderer(upQuery, new UploadIdentification());
        let upReader = new UploaderReader(translations);
        this.upInit = new UploadInit(this, upRenderer, remoteQuery, translations);
        this.upCheck = new UploaderChecker(this, new UploaderChecksum(checksum), upReader, upRenderer, remoteQuery, translations);
        this.upRunner = new UploaderRunner(this, upReader, new UploaderEncoder(), upRenderer, remoteQuery, translations);
        this.upFailure = new UploaderFailure(this, upRenderer, remoteQuery, translations);
        this.upHandler = new UploaderHandler(this, upRenderer);
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    initRead(uploadedFile) {
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
     * Call dumpster render when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    failProcess(uploadedFile, event: any = null) {
        this.upFailure.process(uploadedFile, event);
    }

    /**
     * Call dumpster response when user decide what to do
     * @param {UploadedFile} uploadedFile
     */
    failContinue(uploadedFile) {
        this.upFailure.continue(uploadedFile);
    }

    /**
     * Call dumpster when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    failEnd(uploadedFile, event: any = null) {
        this.upFailure.end(uploadedFile, event);
    }

    /**
     * @returns {UploaderHandler}
     */
    getHandler() {
        return this.upHandler;
    }
}

/**
 * Initial step - prepare file object, ask server about upload details and let him to prepare himself
 */
class UploadInit {
    /** @var {UploaderProcessor} */
    upProcessor = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    upQuery = null;
    /** @var {UploadTranslations} */
    upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    constructor(processor, renderer, query, lang) {
        this.upProcessor = processor;
        this.upRenderer = renderer;
        this.upQuery = query;
        this.upLang = lang;
    }

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    process(uploadedFile) {
        let self = this;
        uploadedFile.startTime = this.upRenderer.getCurrentTime();
        this.upRenderer.updateBar(uploadedFile);
        this.upQuery.init(
            {
                fileName: uploadedFile.fileName,
                fileSize: uploadedFile.fileSize
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // start checking content
                        self.upRenderer.renderReaded(uploadedFile.setInfoFromServer(responseData));
                        self.upProcessor.checkParts(uploadedFile);
                    } else {
                        // uploadedFile.RESULT_FAIL
                        // File is dead, sent user info
                        self.upRenderer.consoleError(uploadedFile, responseData);
                        uploadedFile.setError(self.upLang.initReturnsFollowingError + responseData.errorMessage, responseData.status);
                    }
                } else {
                    // Query dead, sent user info
                    self.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(self.upLang.initReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                self.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
}

/**
 * Check already uploaded parts, trim failed ones
 */
class UploaderChecker {
    /** @var {UploaderProcessor} */
    upProcessor = null;
    /** @var {UploaderChecksum} */
    upChecksum = null;
    /** @var {UploaderReader} */
    upReader = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    upQuery = null;
    /** @var {UploadTranslations} */
    upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderChecksum} checksum
     * @param {UploaderReader} reader
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    constructor(processor, checksum, reader, renderer, query, lang) {
        this.upProcessor = processor;
        this.upChecksum = checksum;
        this.upReader = reader;
        this.upRenderer = renderer;
        this.upQuery = query;
        this.upLang = lang;
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

    nextStep(uploadedFile) {
        if (uploadedFile.STATUS_RUN == uploadedFile.readStatus) {
            this.upProcessor.uploadParts(uploadedFile);
        } else {
            this.upProcessor.failProcess(uploadedFile);
        }
    }

    /**
     * Check data on server - for each already uploaded part
     * @param {UploadedFile} uploadedFile
     */
    checkPart(uploadedFile) {
        let self = this;
        if (this.upChecksum.can()) {
            this.upQuery.check(
                {
                    sharedKey: uploadedFile.sharedKey,
                    segment: uploadedFile.lastCheckedPart
                },
                function(responseData) {
                    if (typeof responseData == "object") {
                        if (uploadedFile.RESULT_OK === responseData.status) {
                            // got known checksum on remote - check it against local file
                            self.upReader.processFileRead(uploadedFile, uploadedFile.lastCheckedPart, function (result) {
                                if (responseData.checksum == self.upChecksum.md5(result)) {
                                    // this part is OK, move to the next one
                                    self.processNext(uploadedFile);
                                } else {
                                    // Check failed, time to truncate
                                    self.truncatePart(uploadedFile);
                                }
                            }, function (event) {
                                self.upRenderer.updateStatus(uploadedFile, event.error);
                            });
                        } else {
                            // failed query
                            self.upProcessor.failProcess(uploadedFile);
                        }
                    } else {
                        // Query dead, sent user info
                        self.upRenderer.consoleError(uploadedFile, responseData);
                        uploadedFile.setError(self.upLang.initReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                    }
                },
                function(err) {
                    self.upProcessor.failProcess(uploadedFile, err);
                }
            );
        } else {
            this.truncateRest(uploadedFile);
        }
    }

    /**
     * callback for processing next part
     * @param uploadedFile
     */
    processNext(uploadedFile) {
        // this part is OK, move to the next one
        this.upRenderer.updateBar(uploadedFile.nextCheckedPart());
        this.continueChecking(uploadedFile);
    }

    /**
     * callback for truncating the rest
     * @param uploadedFile
     */
    truncateRest(uploadedFile) {
        // from this part it's shitty - remove it
        this.truncatePart(uploadedFile);
    }

    /**
     * Truncate failed data on server, to upload them correctly
     * @param {UploadedFile} uploadedFile
     */
    truncatePart(uploadedFile) {
        let self = this;
        this.upQuery.trim(
            {
                sharedKey: uploadedFile.sharedKey,
                segment: uploadedFile.lastCheckedPart
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // Truncate came OK, time to upload the rest
                        self.upRenderer.updateBar(uploadedFile.setTruncatedFromServer(responseData));
                        self.nextStep(uploadedFile);
                    } else {
                        // Truncate failed, time say something
                        self.upProcessor.failProcess(uploadedFile);
                    }
                } else {
                    // Query dead, sent user info
                    self.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(self.upLang.checkerReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                self.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
}

/**
 * Uploading file from client and ask for processing on server side
 */
class UploaderRunner {
    /** @var {UploaderProcessor} */
    upProcessor = null;
    /** @var {UploaderReader} */
    upReader = null;
    /** @var {UploaderEncoder} */
    upEncoder = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    upQuery = null;
    /** @var {UploadTranslations} */
    upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderReader} reader
     * @param {UploaderEncoder} encoder
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    constructor(processor, reader, encoder, renderer, query, lang) {
        this.upProcessor = processor;
        this.upReader = reader;
        this.upEncoder = encoder;
        this.upRenderer = renderer;
        this.upQuery = query;
        this.upLang = lang;
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
            this.uploadPart(uploadedFile);
        } else {
            this.closePart(uploadedFile);
        }
    }

    continueRunning(uploadedFile) {
        if (uploadedFile.STATUS_RUN == uploadedFile.readStatus) {
            this.stillRunning(uploadedFile);
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
        this.upRenderer.updateBar(uploadedFile.nextFilePart());
        this.upReader.processFileRead(uploadedFile, uploadedFile.lastKnownPart, function (result) {
            self.upQuery.upload(
                {
                    sharedKey: uploadedFile.sharedKey,
                    content: self.upEncoder.base64(result),
                    // lastKnownPart: uploadedFile.lastKnownPart
                },
                function(responseData) {
                    if (typeof responseData == "object") {
                        if (uploadedFile.RESULT_OK === responseData.status) {
                            // everything ok
                            self.upRenderer.updateBar(uploadedFile.nextCheckedPart());
                            self.continueRunning(uploadedFile);
                        } else {
                            // dead file, user info
                            self.upProcessor.failProcess(uploadedFile);
                        }
                    } else {
                        self.upRenderer.consoleError(uploadedFile, responseData);
                        uploadedFile.setError(self.upLang.dataUploadReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                    }
                },
                function(err) {
                    self.upRenderer.consoleError(uploadedFile, err);
                }
            );
        }, function (event) {
            self.upProcessor.failProcess(uploadedFile, event);
        });
    }

    /**
     * Send request about upload closure
     * @param {UploadedFile} uploadedFile
     */
    closePart(uploadedFile) {
        let self = this;
        this.upQuery.done(
            {
                sharedKey: uploadedFile.sharedKey
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // everything ok
                        uploadedFile.readStatus = uploadedFile.STATUS_FINISH;
                        self.upRenderer.renderFinished(uploadedFile);
                    } else {
                        // dead file, user info
                        self.upProcessor.failProcess(uploadedFile);
                    }
                } else {
                    // dead query, user info
                    self.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(self.upLang.doneReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                self.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
}

/**
 * When upload fails then it's necessary to have user-specific action. So here call that.
 */
class UploaderFailure {
    /** @var {UploaderProcessor} */
    upProcessor = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    upQuery = null;
    /** @var {UploadTranslations} */
    upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    constructor(processor, renderer, query, lang) {
        this.upProcessor = processor;
        this.upRenderer = renderer;
        this.upQuery = query;
        this.upLang = lang;
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
            this.upProcessor.initRead(uploadedFile);
        } else if (uploadedFile.STATUS_RETRY == uploadedFile.status) {
            this.upProcessor.checkPart(uploadedFile.setInfoFromClearer());
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
        this.upQuery.cancel(
            {
                sharedKey: uploadedFile.sharedKey
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // everything done
                        self.checkContinue(uploadedFile);
                    } else {
                        // dead file, user info
                        self.end(uploadedFile);
                    }
                } else {
                    // dead query, user info
                    self.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(self.upLang.doneReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                self.upRenderer.consoleError(uploadedFile, err);
            }
        );
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

/**
 * Class for reading selected file on client's storage
 */
class UploaderReader {
    /** @var {UploadTranslations} */
    upLang = null;

    /**
     * @param {UploadTranslations} upLang
     */
    constructor(upLang) {
        this.upLang = upLang;
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
            uploadedFile.setError(this.upLang.readFileCannotSlice, uploadedFile.RESULT_FAIL);
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

    /**
     * @param {*} window
     */
    static canRead(window: any) {
        return window.File && window.FileReader && window.FileList && window.Blob
    }
}

/**
 * Encode binary file chunk into base64 to prevent problems with text-based transportation
 */
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

/**
 * Class for making checksum of segments, usually use MD5
 */
class UploaderChecksum {
    /** @var {CheckSumMD5} */
    checksum = null;

    /**
     * @param {CheckSumMD5} checksum
     */
    constructor(checksum = null) {
        this.checksum = checksum;
    }

    can() {
        return (null != this.checksum);
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

/**
 * Abstract class for enabling different engines for Ajax call and content selectors, not just jQuery
 */
class UploaderQuery {
    /** @var {jQuery} */
    queryEngine = null;

    /**
     * @param {jQuery} queryEngine
     */
    constructor(queryEngine) {
        this.queryEngine = queryEngine;
    }

    /**
     * Post query on remote system
     * @param {string} target
     * @param {Object} params
     * @param {function} onSuccess
     * @param {function} onError
     */
    post(target, params, onSuccess, onError) {
        this.queryEngine.post(target, params).done(onSuccess).fail(onError);
    }

    /**
     * @param {string} ident
     * @returns {jQuery|*}
     */
    getObjectById(ident) {
        return this.queryEngine('#' + ident);
    }

    /**
     * @param {string} ident
     * @param {string} content
     */
    setObjectContent(ident, content) {
        this.queryEngine('#' + ident).text(content);
    }
}

/**
 * Queries to remote machine
 * Should be extended in tests for mocking the querying to behave likewise with the remote machine
 */
class UploaderRemoteQuery {
    /** @var {UploaderQuery} */
    queryLib = null;
    /** @var {UploadTargetConfig} */
    targetLinks = null;

    /**
     * @param {UploaderQuery} queryLib
     * @param {UploadTargetConfig} targetLinks
     */
    constructor(queryLib, targetLinks) {
        this.queryLib = queryLib;
        this.targetLinks = targetLinks;
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    init(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetInitPath,
            queryParams,
            onSuccess,
            onError
        );
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    check(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetCheckPath,
            queryParams,
            onSuccess,
            onError
        );
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    trim(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetTrimPath,
            queryParams,
            onSuccess,
            onError
        );
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    upload(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetFilePath,
            queryParams,
            onSuccess,
            onError
        );
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    done(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetDonePath,
            queryParams,
            onSuccess,
            onError
        );
    }

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    cancel(queryParams, onSuccess, onError) {
        this.queryLib.post(
            this.targetLinks.targetCancelPath,
            queryParams,
            onSuccess,
            onError
        );
    }
}

/**
 * Handle input from browser
 */
class UploaderHandler {
    /** @type {UploadedFile[]} */
    uploadingFiles = []; // All uploaded files in JS
    /** @var {UploaderProcessor} */
    upProcessor = null;
    /** @var {UploaderRenderer} */
    upRenderer = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     */
    constructor(processor, renderer) {
        this.upProcessor = processor;
        this.upRenderer = renderer;
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
            this.upRenderer.renderFileItem(dataSource);
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
            this.upProcessor.initRead(file);
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
            file.readStatus = file.STATUS_STOP;
            // it stops with next tick
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
            file.readStatus = file.STATUS_RETRY;
            this.upProcessor.failContinue(file);
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
    retryRead(fileId) {
        let file = this.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_INIT;
            this.upProcessor.failContinue(file);
        } else {
            // all recursive
            for (let i = 0; i < this.uploadingFiles.length; i++) {
                this.retryRead(this.uploadingFiles[i].localId);
            }
        }
    }

    /**
     * @param {string} fileId
     */
    abortRead(fileId) {
        let file = this.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_DESTROY;
            this.upProcessor.failContinue(file);
        } else {
            // all recursive
            for (let i = 0; i < this.uploadingFiles.length; i++) {
                this.abortRead(this.uploadingFiles[i].localId);
            }
        }
    }

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
        this.upRenderer.clearFileSelection();
    }
}

/**
 * Render output to browser
 * Usually main candidate to customization
 */
class UploaderRenderer {

    /** @var {UploadIdentification} */
    upIdent = null;
    /** @var {UploaderQuery} */
    upQuery = null;

    /**
     * @param {UploaderQuery} upQuery
     * @param {UploadIdentification} upIdent
     */
    constructor(upQuery, upIdent) {
        this.upQuery = upQuery;
        this.upIdent = upIdent;
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    renderFileItem(uploadedFile) {
        let progress: any = this.upQuery.getObjectById(this.upIdent.baseProgress);
        let progress_bar: any = progress.clone(true);
        progress_bar.attr(this.upIdent.localId, uploadedFile.localId);
        let list: any = this.upQuery.getObjectById(this.upIdent.knownBulk);
        list.append(progress_bar);
        let fileName: any = progress_bar.find(".filename").eq(1);
        fileName.append(uploadedFile.fileName);
        let button1: any = progress_bar.find("button").eq(1);
        button1.attr(this.upIdent.dataKey, uploadedFile.localId);
        let button2: any = progress_bar.find("button").eq(2);
        button2.attr(this.upIdent.dataKey, uploadedFile.localId);
        let button3: any = progress_bar.find("button").eq(3);
        button3.attr(this.upIdent.dataKey, uploadedFile.localId);
    }

    clearFileSelection() {
        let list: any = this.upQuery.getObjectById(this.upIdent.knownBulk);
        list.children().remove();
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    renderReaded(uploadedFile) {
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let button: any = node.find("button").eq(1);
        button.removeAttr("disabled");
        node.attr(this.upIdent.localId, uploadedFile.sharedKey);
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    renderFinished(uploadedFile) {
        this.upQuery.setObjectContent(this.upIdent.elapsedTime, UploaderRenderer.formatTime(UploaderRenderer.getElapsedTime(uploadedFile)));
        this.upQuery.setObjectContent(this.upIdent.estimatedTimeLeft, UploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, 100));
        this.upQuery.setObjectContent(this.upIdent.currentPosition, UploaderRenderer.calculateSize(uploadedFile.fileSize));
        this.upQuery.setObjectContent(this.upIdent.totalSize, UploaderRenderer.calculateSize(uploadedFile.fileSize));
        this.upQuery.setObjectContent(this.upIdent.estimatedSpeed, UploaderRenderer.calculateSize(0));
        this.upQuery.setObjectContent(this.upIdent.percentsComplete, "100%"); // percents

        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
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
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.removeAttr("disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    stopRead(uploadedFile) {
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.attr("disabled", "disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    resumeRead(uploadedFile) {
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let button: any = node.find("button").eq(2);
        button.removeAttr("disabled");
    }

    /**
     * @param {UploadedFile} uploadedFile
     */
    updateBar(uploadedFile) {
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let percent = UploaderRenderer.calculatePercent(uploadedFile);

        this.upQuery.setObjectContent(this.upIdent.elapsedTime, UploaderRenderer.formatTime(UploaderRenderer.getElapsedTime(uploadedFile)));
        this.upQuery.setObjectContent(this.upIdent.estimatedTimeLeft, UploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, percent));
        this.upQuery.setObjectContent(this.upIdent.currentPosition, UploaderRenderer.calculateSize(uploadedFile.lastKnownPart * uploadedFile.partSize));
        this.upQuery.setObjectContent(this.upIdent.totalSize, UploaderRenderer.calculateSize(uploadedFile.fileSize));
        this.upQuery.setObjectContent(this.upIdent.estimatedSpeed, UploaderRenderer.calculateSize(UploaderRenderer.calculateSpeed(uploadedFile)));
        this.upQuery.setObjectContent(this.upIdent.percentsComplete, percent.toString() + "%");

        let button: any = node.find("." + this.upIdent.progressCounter).eq(1);
        button.append(percent.toString() + "%");
        button.css('width', percent.toString() + "%");
    }

    /**
     * @param {UploadedFile} uploadedFile
     * @param {string} status
     */
    updateStatus(uploadedFile, status) {
        let node: any = this.upQuery.getObjectById(uploadedFile.localId);
        let errLog: any = node.find("." + this.upIdent.errorLog).eq(1);
        errLog.append(status);
    }

    /**
     * calculate passed time
     * @param {UploadedFile} uploadedFile
     * @param {numeric} percentDone
     * @return {string}
     */
    static calculateEstimatedTimeLeft (uploadedFile, percentDone) {
        let spend = UploaderRenderer.getElapsedTime(uploadedFile);
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
        let str = "" + number.toString();
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
        return !percent ? 0 : percent * 100;
    }

    /**
     * calculate processing speed - bytes/second
     * @param {UploadedFile} uploadedFile
     * @return {numeric}
     */
    static calculateSpeed (uploadedFile) {
        let elapsedTime = UploaderRenderer.getElapsedTime(uploadedFile);

        if (elapsedTime < 1) {
            return 0;
        }
        if (0 == uploadedFile.lastKnownPart) {
            return 0;
        }
        return (uploadedFile.totalParts * elapsedTime * 100) / uploadedFile.lastKnownPart;
    }

    /**
     * calculate sizes
     * @param {number} bytesProcessed
     * @return {string}
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
     * @param {UploadedFile} uploadedFile
     * @return {number}
     */
    static getElapsedTime (uploadedFile) {
        return UploaderRenderer.getCurrentTime() - uploadedFile.startTime;
    }

    /**
     * current time for init and calculations
     * @return {number}
     */
    static getCurrentTime () {
        return Math.round(new Date().getTime() / 1000);
    }
}
